<?php

namespace App\Http\Controllers;

use App\Mail\MassPayment;
use App\Models\Client;
use App\Models\Cost;
use App\Models\CostType;
use App\Models\Invoice;
use App\Models\Quest;
use App\Models\Request as ModelsRequest;
use App\Models\Status;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StatsController extends Controller
{
    public function dashboard(){
        $stats = json_decode(Storage::get("/stats.json"));
        $stats->today = Carbon::parse($stats->today)->diffForHumans();

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact("stats"),
        ));
    }
    public function statsImport(Request $rq){
        $rq->file("json")->storeAs("/", "stats.json");
        return back()->with("success", "Dane zaktualizowane");
    }

    public function financeDashboard(){
        $unpaids_raw = Quest::where("paid", 0)
            ->whereNotIn("status_id", [17, 18])
            ->whereHas("client", function($query){
                $query->where("trust", ">", -1);
            })
            ->orderBy("quests.updated_at")
            ->get();
        $unpaids = [];
        if(count($unpaids_raw) > 0){
            foreach($unpaids_raw as $quest){
                $unpaids[$quest->client->id][] = $quest;
            };
        }

        $recent = StatusChange::where("new_status_id", 32)->orderByDesc("date")->limit(10)->get();
        foreach($recent as $i){
            $i->quest = Quest::find($i->re_quest_id);
            $i->new_status = Status::find($i->new_status_id);
        }

        return view(user_role().".finance", array_merge(
            ["title" => "Centrum Finansowe"],
            compact(
                "unpaids", "recent"
            ),
        ));
    }
    public function financePay(Request $rq){
        $quest_ids = array_keys($rq->except("_token"));

        $clients_quests = [];
        foreach($quest_ids as $id){
            $quest = Quest::find($id);

            // opłać zlecenia
            app("App\Http\Controllers\BackController")->statusHistory(
                $id,
                32,
                $quest->price - $quest->payments->sum("comment"),
                $quest->client_id,
                $quest->client->isMailable()
            );
            $quest->update(["paid" => (StatusChange::where(["new_status_id" => 32, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // zbierz zlecenia dla konkretnych adresatów
            $clients_quests[$quest->client_id][] = $quest;
        }

        // roześlij maile, jeśli można
        foreach($clients_quests as $client_id => $quests){
            $client = Client::find($client_id);
            if($client->isMailable()){
                Mail::to($quest->client->email)->send(new MassPayment($quests));
            }
        }

        return back()->with("success", "Zlecenia opłacone");
    }

    public function invoice($id){
        $invoice = Invoice::findOrFail($id);

        return view(user_role().".invoice", array_merge(
            ["title" => "Faktura nr ".$invoice->fullCode()],
            compact("invoice"),
        ));
    }
    public function invoiceVisibility(Request $rq){
        Invoice::find($rq->id)->update(["visible" => $rq->visible]);

        return back()->with("success", $rq->visible ? "Faktura widoczna" : "Faktura schowana");
    }
    public function invoiceAdd(Request $rq){
        $quest = Quest::find($rq->quest_id);

        Invoice::create([
            "quest_id" => $quest->id,
            "primary" => ($quest->allInvoices()->count() == 0),
            "visible" => false,
            "amount" => $quest->price - $quest->allInvoices()->sum("paid"),
            "paid" => ($quest->paid ? $quest->price - $quest->allInvoices()->sum("paid") : 0),
            "payer_name" => $rq->payer_name,
            "payer_title" => $rq->payer_title,
            "payer_address" => $rq->payer_address,
            "payer_nip" => $rq->payer_nip,
            "payer_regon" => $rq->payer_regon,
            "payer_email" => $rq->payer_email,
            "payer_phone" => $rq->payer_phone,
        ]);

        return back()->with("success", "Dokument utworzony");
    }

    public function costs(){
        $costs = Cost::orderByDesc("created_at")->paginate(25);
        $types = CostType::all()->pluck("name", "id");

        return view(user_role().".costs", array_merge(
            ["title" => "Lista kosztów"],
            compact("costs", "types"),
        ));
    }
    public function modCost(Request $rq){
        $fields = [
            "cost_type_id" => $rq->cost_type_id,
            "desc" => $rq->desc,
            "amount" => $rq->amount,
        ];
        if($rq->id) Cost::find($rq->id)->update($fields);
        else Cost::create($fields);

        return back()->with("success", "Gotowe");
    }
    public function costTypes(){
        $types = CostType::all();

        return view(user_role().".cost-types", array_merge(
            ["title" => "Typy kosztów"],
            compact("types"),
        ));
    }
    public function modCostType(Request $rq){
        $fields = ["name" => $rq->name, "desc" => $rq->desc];
        if($rq->id) CostType::find($rq->id)->update($fields);
        else CostType::create($fields);

        return back()->with("success", "Gotowe");
    }

    public function fileSizeReport(){
        $safes = Storage::disk()->directories("safe");
        $sizes = []; $times = [];
        foreach($safes as $safe){
            $files = Storage::files($safe);
            $size = 0;
            $modtime = 0;
            foreach($files as $file){
                $size += Storage::size($file);
                if(Storage::lastModified($file) > $modtime) $modtime = Storage::lastModified($file);
            }
            $sizes[$safe] = $size;
            $times[$safe] = new Carbon($modtime);
        }
        arsort($sizes);

        return view(user_role().".file-size-report", array_merge(
            ["title" => "Raport zajętości serwera"],
            compact(
                "sizes", "times",
            ),
        ));
    }

    public function questsCalendar(){
        $calendar_length = max(
            7,
            Quest::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays() + 2,
            ModelsRequest::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays() + 2,
        );

        return view(user_role().".quests-calendar", array_merge(
            ["title" => "Grafik zleceń"],
            compact(
                "calendar_length"
            ),
        ));
    }
}
