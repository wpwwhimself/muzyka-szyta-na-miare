<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageQuestMod;
use App\Mail\Clarification;
use App\Mail\PaymentReceived;
use App\Mail\QuestRequoted;
use App\Mail\QuestUpdated;
use App\Models\Invoice;
use App\Models\InvoiceQuest;
use App\Models\Quest;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\StatusChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestController extends Controller
{
    public function list(HttpRequest $rq){
        $status_id = $rq->status;
        $paid = $rq->paid;

        $client = User::find(is_archmage() ? $rq->client : Auth::id());

        $quests = Quest::orderBy("quests.created_at", "desc");
        if($client){ $quests = $quests->where("client_id", $client->id); }
        if (is_archmage()) {
            if($status_id) $quests = $quests->where("status_id", $status_id);
            if($paid) $quests = $quests->where("paid", $paid);
        }

        $quests = $quests->paginate(25);

        return view("pages.".user_role().".quests", [
            "title" => ($client && is_archmage()) ? "$client->client_name – zlecenia" : "Lista zleceń",
            "quests" => $quests
        ]);
    }

    public function show($id){
        $quest = Quest::findOrFail($id);

        $prices = DB::table("prices")
            ->where("quest_type_id", $quest->song->type->id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")
            ->orderBy("indicator")
            ->get()
            ->map(fn ($p) => "<strong>$p->indicator</strong>: $p->service")
            ->join("<br>");
        if($quest->client_id != Auth::id() && !is_archmage()) abort(403, "To nie jest Twoje zlecenie");

        $files = $quest->song->files
            ->groupBy("variant_name");

        $warnings = is_archmage() ? [
            "files" => [
                'Pliki nieoznaczone jako komplet' => $quest->status_id != 11 && !$quest->files_ready,
            ],
            "quote" => [
                'Ostatnia zmiana padła '.$quest->history->first()?->date->diffForHumans() => in_array($quest->status_id, [16, 26]) && $quest->history->first()?->date->diffInDays() >= 30,
                'Opóźnienie wpłaty' => $quest->delayed_payment_in_effect,
            ],
        ] : [
            "quote" => [
                'Zwróć uwagę, kiedy masz zapłacić' => !!$quest->delayed_payment_in_effect,
                'Zlecenie nieopłacone' => $quest->user->notes->trust == -1
                    || $quest->status_id == 19 && !$quest->paid
                    || $quest->payments_sum > 0 && $quest->payments_sum < $quest->price,
            ],
        ];

        return view("pages.".user_role().".quest", array_merge(
            ["title" => "Zlecenie"],
            compact("quest", "prices", "files", "warnings"),
            (isset($stats_statuses) ? compact("stats_statuses") : []),
        ));
    }

    ////////////////////////////////////////

    public function processMod(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $quest = Quest::findOrFail($rq->quest_id);
        if(SongWorkTime::where(["song_id" => $quest->song_id, "now_working" => 1])->first()){
            return back()->with("toast", ["error", "Zatrzymaj zegar"]);
        }

        // wpisywanie wpłaty za zlecenie
        if($rq->status_id == 32){
            if(empty($rq->comment)) return redirect()->route("quest", ["id" => $rq->quest_id])->with("toast", ["error", "Nie podałeś ceny"]);

            // opłacenie zlecenia (sprawdzenie nadwyżki)
            $amount_to_pay = $quest->price - $quest->payments_sum;
            $amount_for_budget = $rq->comment - $amount_to_pay;

            BackController::newStatusLog($rq->quest_id, $rq->status_id, min($rq->comment, $amount_to_pay), $quest->client_id);
            if($amount_for_budget > 0){
                BackController::newStatusLog(null, $rq->status_id, $amount_for_budget, $quest->client_id);

                // budżet
                $quest->client->budget += $amount_for_budget;
                $quest->client->save();
            }

            // opłacanie faktury
            $invoice = InvoiceQuest::where("quest_id", $rq->quest_id)
                ->get()
                ->filter(fn($val) => !($val->isPaid))
                ->first();
            $invoice?->update(["paid" => $invoice->paid + $rq->comment]);
            // opłacanie faktury macierzystej
            $invoice = $invoice?->mainInvoice;
            $invoice?->update(["paid" => $invoice->paid + $rq->comment]);

            $quest->update(["paid" => (StatusChange::where(["new_status_id" => $rq->status_id, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // sending mail
            $flash_content = "Cena wpisana";
            if($quest->paid){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new PaymentReceived($quest->fresh()));
                    StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => true]);
                    $flash_content .= ", mail wysłany";
                }
                if($quest->client->contact_preference != "email"){
                    // StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => false]);
                    $flash_content .= ", ale wyślij wiadomość";
                }
            }

            // wycofanie statusu krętacza
            if ($quest->client->trust == -1 && $quest->client->quests_unpaid->count() == 0) {
                $quest->client->update(["trust" => 0]);
                $flash_content .= "; już nie jest krętaczem";
            }

            return redirect()->route("quest", ["id" => $rq->quest_id])->with("toast", ["success", $flash_content]);
        }

        $is_same_status = $quest->status_id == $rq->status_id;
        $quest->status_id = $rq->status_id;

        // files ready checkpoint
        if(in_array($rq->status_id, [16, 26])){
            $quest->files_ready = false;
        }

        // handle rejecting new quote
        $last_status = $quest->history->first();
        if ($last_status->new_status_id == 31 && $rq->status_id == 19) {
            $fields = [
                "cena" => "price",
                "kod wyceny" => "price_code_override",
                "do kiedy (włącznie) oddam pliki" => "deadline",
                "opóźnienie wpłaty" => "delayed_payment",
            ];

            $changes = json_decode($last_status->values);

            foreach ($changes as $label => $change) {
                if ($label == "zmiana z uwagi na") continue;

                [$prev, $next] = explode(" → ", $change);
                $quest->{$fields[$label]} = empty($prev) ? null : $prev;
            }
            $quest->paid = true;
        }

        $quest->save();

        if($is_same_status){
            $quest->history->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            BackController::newStatusLog(
                $rq->quest_id,
                $rq->status_id,
                $rq->comment,
                (is_archmage() && in_array($rq->status_id, [16, 18, 19, 21, 26, 96])) ? $quest->client_id : null
            );
        }

        // sending mail
        $flash_content = "Faza zmieniona";
        $mailing = null;
        if(
            in_array($quest->status_id, [15, 95])
            || $quest->status_id == 11 && is_archmage()
        ){ // mail do klienta
            if($quest->client->email){
                Mail::to($quest->client->email)->send(
                    $quest->status_id == 95
                    ? new Clarification($quest->fresh())
                    : new QuestUpdated($quest->fresh())
                );
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($quest->client->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if(!is_archmage()){ // mail do mnie
            Mail::to(env("MAIL_MAIN_ADDRESS"))->send(new ArchmageQuestMod($quest->fresh()));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $quest->fresh()->history->first()->update(["mail_sent" => $mailing]);

        return redirect()->route("quest", ["id" => $rq->quest_id])->with("toast", ["success", $flash_content]);
    }

    public function patch($id, $mode = "key-value", HttpRequest $rq){
        $data = Quest::findOrFail($id);
        if($mode == "single"){
            $data->{$rq->key} = $rq->value;
        }elseif($mode == "key-value"){
            foreach($rq->all() as $key => $value){
                $data->{Str::snake($key)} = $value;
            }
        }
        $data->save();
        return response()->json(["patched" => $rq->all(), "quest" => $data]);
    }

    ////////////////////////////////////////

    public function updateSong(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $song = Song::findOrFail($rq->id);
        $song->update([
            "title" => $rq->title,
            "artist" => $rq->artist,
            "link" => yt_cleanup($rq->link),
            "notes" => $rq->wishes,
        ]);
        return back()->with("toast", ["success", "Utwór zmodyfikowany"]);
    }

    public function updateWishes(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $quest = Quest::findOrFail($rq->id);
        $quest->update([
            "wishes" => $rq->wishes_quest,
        ]);
        return back()->with("toast", ["success", "Zlecenie zmodyfikowane"]);
    }

    public function updateQuote(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $quest = Quest::findOrFail($rq->id);
        $price_before = $quest->price;
        $price_code_before = $quest->price_code_override;
        $deadline_before = $quest->deadline;
        $delayed_payment_before = $quest->delayed_payment;
        $price_data = StatsController::runPriceCalc($rq->price_code_override, $quest->client_id);
        $quest->update([
            "price_code_override" => $rq->price_code_override,
            "price" => $price_data["price"],
            "paid" => ($quest->payments_sum >= $price_data["price"]),
            "deadline" => $rq->deadline,
            "delayed_payment" => $rq->delayed_payment,
        ]);
        $difference = $quest->price - $price_before;
        if($quest->client->budget){
            $sub_amount = min([$difference, $quest->client->budget]);
            $quest->client->budget -= $sub_amount;
            BackController::newStatusLog(null, 32, -$sub_amount, $quest->client->id);
            if($sub_amount == $difference){
                $quest->paid = true;
                $quest->save();
            }
            $quest->client->save();
            BackController::newStatusLog($quest->id, 32, $sub_amount, $quest->client->id);
        }

        if($price_before != $quest->price){
            InvoiceQuest::where("quest_id", $quest->id)->update(["amount" => $quest->price]);
            $invoice_amount = Invoice::whereHas("quests", fn($q) => $q->where("quest_id", $quest->id))->first()?->quests->sum("price");
            Invoice::whereHas("quests", fn($q) => $q->where("quest_id", $quest->id))->update(["amount" => $invoice_amount]);
        }

        // sending mail
        $mailing = null;
        if($quest->client->email){
            Mail::to($quest->client->email)->send(new QuestRequoted($quest->fresh(), $rq->reason, $difference));
            $mailing = true;
        }
        if($quest->client->contact_preference != "email"){
            $mailing ??= false;
        }

        // zbierz zmiany
        $changes = [];
        foreach([
            "cena" => [$price_before, $quest->price],
            "do kiedy (włącznie) oddam pliki" => [$deadline_before?->format("Y-m-d"), $quest->deadline?->format("Y-m-d")],
            "opóźnienie wpłaty" => [$delayed_payment_before?->format("Y-m-d"), $quest->delayed_payment?->format("Y-m-d")],
            "kod wyceny" => [$price_code_before, $quest->price_code_override],
        ] as $attr => $value){
            if ($value[0] != $value[1]) $changes[$attr] = $value[0] . " → " . $value[1];
        }
        $changes["zmiana z uwagi na"] = $rq->reason;

        // change quest status
        $quest->update([
            "status_id" => 31,
            "files_ready" => false,
        ]);

        BackController::newStatusLog(
            $rq->id,
            31,
            null,
            null,
            $mailing,
            $changes
        );
        return back()->with("toast", ["success", "Wycena zapytania zmodyfikowana"]);
    }

    public function updateFilesReady(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $quest = Quest::findOrFail($rq->quest_id);
        $quest->update([
            "files_ready" => $rq->ready,
        ]);
        return back()->with("toast", ["success", "Zawartość sejfu zatwierdzona"]);
    }

    public function updateFilesExternal(HttpRequest $rq) {
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $quest = Quest::findOrFail($rq->quest_id);

        $quest->update([
            "has_files_on_external_drive" => $rq->external,
        ]);
        return back()->with("toast", ["success", "Zmieniono status chmury"]);
    }
}
