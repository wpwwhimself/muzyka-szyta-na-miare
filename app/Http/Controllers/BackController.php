<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageQuestMod;
use App\Mail\Clarification;
use App\Mail\PatronRejected;
use App\Mail\PaymentReceived;
use App\Mail\QuestRequoted;
use App\Mail\QuestUpdated;
use App\Mail\RequestQuoted;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceQuest;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

        $requests = Request::whereNotIn("status_id", [4, 7, 8, 9])
            ->orderByDesc("updated_at");
        $quests = Quest::whereNotIn("status_id", [17, 18, 19])
            ->orderByRaw("case when price_code_override regexp 'z' and status_id in (11, 12, 16, 26, 96) then 0 else 1 end") //najpierw priorytety
            ->orderByRaw("case status_id when 13 then 1 else 0 end")
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderByRaw("case status_id
                when 12 then 1
                when 16 then 2
                when 96 then 3
                when 26 then 4
                when 11 then 5
                when 95 then 6
                when 15 then 7
                else 99
            end")
            ->orderBy("deadline")
            ->orderByRaw("paid desc")
            ->orderBy("created_at");

        if(!in_array(Auth::id(), [0, 1], true)){
            $requests = $requests->where("client_id", $client->id);
            $quests_total = Auth::user()->client->exp;
            $unpaids = Quest::where("client_id", Auth::id())
                ->whereNotIn("status_id", [18])
                ->where("paid", 0)
                ->get();
        }else{
            $recent = StatusChange::whereNotIn("new_status_id", [9, 32])
                ->where(fn($q) => $q
                    ->where("changed_by", "!=", 1)
                    ->orWhereNull("changed_by")
                )
                ->orderByDesc("date")
                ->limit(7)
                ->get();
            foreach($recent as $change){
                $change->is_request = (strlen($change->re_quest_id) == 36);
                $change->re_quest = ($change->is_request) ?
                    Request::find($change->re_quest_id) :
                    Quest::find($change->re_quest_id);
                $change->new_status = Status::find($change->new_status_id);
            }
            $patrons_adepts = Client::where("helped_showcasing", 1)->get();
            $gains = [
                "this_month" => StatusChange::where("new_status_id", 32)
                    ->whereDate("date", ">=", Carbon::today()->subMonth())
                    ->sum("comment"),
                "last_month" => StatusChange::where("new_status_id", 32)
                    ->whereDate("date", "<", Carbon::today()->subMonth())
                    ->whereDate("date", ">=", Carbon::today()->subMonths(2))
                    ->sum("comment"),
                "total" => StatusChange::where("new_status_id", 32)
                    ->sum("comment"),
            ];
            $gains["monthly_diff"] = $gains["this_month"] - $gains["last_month"];
            $janitor_log = json_decode(Storage::get("janitor_log.json")) ?? [];
            foreach($janitor_log as $i){
                $i->re_quest = ($i->is_request) ? Request::find($i->re_quest->id) : Quest::find($i->re_quest->id);
            }
            $gains_this_month = StatusChange::whereDate("date", ">=", Carbon::today()->floorMonth())->sum("comment");
        }
        $requests = $requests->get();
        $quests = $quests->get();

        return view(user_role().".dashboard", array_merge(
            [
                "title" => (in_array(Auth::id(), [0, 1], true))
                ? (Auth::id() == 1 ? "Szpica arcymaga" : "WITAJ, OBSERWATORZE")
                : "Pulpit"
            ],
            compact("quests", "requests"),
            (isset($quests_total) ? compact("quests_total") : []),
            (isset($patrons_adepts) ? compact("patrons_adepts") : []),
            (isset($unpaids) ? compact("unpaids") : []),
            (isset($gains) ? compact("gains") : []),
            (isset($gains_this_month) ? compact("gains_this_month") : []),
            (isset($janitor_log) ? compact("janitor_log") : []),
            (isset($recent) ? compact("recent") : []),
        ));
    }

    public function prices(){
        $prices = DB::table("prices")->get();

        $discount = (in_array(Auth::id(), [0, 1], true)) ? null : (
            (Auth::user()->client->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
            +
            (Auth::user()->client->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
        );

        $quest_types = QuestType::all()->pluck("type", "id")->toArray();
        $minimal_prices = array_combine($quest_types, QUEST_MINIMAL_PRICES());

        return view(user_role().".prices", array_merge(
            ["title" => "Cennik"],
            compact("prices", "discount", "minimal_prices")
        ));
    }

    public function requests(HttpRequest $rq){
        $client = Auth::user()->client;
        $client_id = $rq->client;
        $status_id = $rq->status;

        $requests = Request::orderBy("updated_at", "desc");
        if(!in_array(Auth::id(), [0, 1], true)){ $requests = $requests->where("client_id", $client->id); }
        if($client_id) $requests = $requests->where("client_id", $client_id);
        if($status_id) $requests = $requests->where("status_id", $status_id);
        $requests = $requests->paginate(25);

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }
    public function request($id){
        $request = Request::find($id);
        if(!$request) abort(404, "Nie ma takiego zapytania");
        $pad_size = 30; // used by dropdowns for mobile preview fix

        if(in_array(Auth::id(), [0, 1], true)){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = _ct_("$client[client_name] «$client[id]»");
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = Str::limit($song["title"] ?? "bez tytułu ($song[artist])", $pad_size)." «$song[id]»";
            }
        }else{
            if($request->client_id != Auth::id()){
                if(Auth::id()) abort(403, "To nie jest Twoje zapytanie");
                else return redirect()->route("login")->with("error", "Zaloguj się, jeśli to Twoje zapytanie");
            };
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")
            ->where("quest_type_id", $request->quest_type_id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres")));
    }
    public function addRequest(){
        $pad_size = 24; // used by dropdowns for mobile preview fix

        if(in_array(Auth::id(), [0, 1], true)){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = _ct_("$client[client_name] «$client[id]»");
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = Str::limit($song["title"] ?? "bez tytułu ($song[artist])", $pad_size)." «$song[id]»";
            }
        }else{
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs", "genres")));
    }

    public function addRequestBack(HttpRequest $rq){
        if(isset($rq->m_test) && $rq->m_test != 20) return redirect()->route("home")->with("error", "Cztery razy pięć nie równa się $rq->m_test");
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());

        $flash_content = "Zapytania dodane";
        $loop_length = is_array($rq->quest_type) ? count($rq->quest_type) : 1;
        $requests_created = [];

        for($i = 0; $i < $loop_length; $i++){
            if(in_array(Auth::id(), [0, 1], true)){ // arcymag
                //non-bulk
                $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
                $client = ($rq->client_id) ? Client::find($rq->client_id) : null;

                $request = Request::create([
                    "made_by_me" => true,

                    "client_id" => $rq->client_id,
                    "client_name" => $rq->client_name,
                    "email" => $rq->email,
                    "phone" => $rq->phone,
                    "other_medium" => $rq->other_medium,
                    "contact_preference" => $rq->contact_preference ?? "email",

                    "song_id" => $rq->song_id,
                    "quest_type_id" => $rq->quest_type,
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => $rq->link,
                    "genre_id" => $rq->genre_id,
                    "wishes" => $rq->wishes,
                    "wishes_quest" => $rq->wishes_quest,

                    "price_code" => price_calc($rq->price_code, $rq->client_id, true)["labels"],
                    "price" => price_calc($rq->price_code, $rq->client_id, true)["price"],
                    "deadline" => $rq->deadline,
                    "hard_deadline" => $rq->hard_deadline,
                    "delayed_payment" => $rq->delayed_payment,
                    "status_id" => $rq->new_status,
                ]);

                // nadpisanie zmienionych gotowców
                if($rq->song_id){
                    Song::find($rq->song_id)->update([
                        "title" => $rq->title,
                        "artist" => $rq->artist,
                        "link" => $rq->link,
                        "genre_id" => $rq->genre_id,
                        "price_code" => preg_replace("/[=\-oyzqr\d]/", "", $rq->price_code),
                        "notes" => $rq->wishes,
                    ]);
                }
                if($rq->client_id){
                    Client::find($rq->client_id)->update([
                        "client_name" => $rq->client_name,
                        "email" => $rq->email,
                        "phone" => $rq->phone,
                        "other_medium" => $rq->other_medium,
                        "contact_preference" => $rq->contact_preference,
                    ]);
                }

                if($rq->new_status == 5) $this->statusHistory($request->id, 1, null);
                $this->statusHistory($request->id, $rq->new_status, $rq->comment);

                //mailing
                $mailing = null;
                if(
                    $request->email &&
                    $rq->new_status == 5
                ){
                    Mail::to($request->email)->send(new RequestQuoted($request));
                    $mailing = true;
                    $flash_content .= ", mail wysłany";
                }
                if($request->contact_preference != "email"){
                    $mailing ??= false;
                    $flash_content .= ", ale wyślij wiadomość";
                }
                if($mailing !== null) $request->changes->first()->update(["mail_sent" => $mailing]);
            }else{ // klient
                //bulk
                $request = Request::create([
                    "made_by_me" => false,

                    "client_id" => (Auth::check()) ? Auth::id() : null,
                    "client_name" => (Auth::check()) ? Auth::user()->client->client_name : $rq->client_name,
                    "email" => (Auth::check()) ? Auth::user()->client->email : $rq->email,
                    "phone" => (Auth::check()) ? Auth::user()->client->phone : $rq->phone,
                    "other_medium" => (Auth::check()) ? Auth::user()->client->other_medium : $rq->other_medium,
                    "contact_preference" => (Auth::check()) ? Auth::user()->client->contact_preference : $rq->contact_preference,

                    "quest_type_id" => $rq->quest_type[$i],
                    "title" => $rq->title[$i],
                    "artist" => $rq->artist[$i],
                    "link" => $rq->link[$i],
                    "wishes" => $rq->wishes[$i],

                    "hard_deadline" => $rq->hard_deadline[$i],
                    "status_id" => $rq->new_status,
                ]);

                //mailing do mnie
                $mailing = null;
                Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request));
                $mailing = true;

                $this->statusHistory($request->id, $rq->new_status, $rq->wishes[$i], (Auth::check()) ? Auth::id() : null, $mailing);
            }
            $requests_created[] = $request;
        }

        if(Auth::check()) return redirect()->route("dashboard")->with("success", $flash_content);
        return view("client.request-confirm", array_merge(
            ["title" => "Zapytanie dodane"],
            compact("requests_created")
        ))->with("success", $flash_content);
    }

    public function modRequestBack(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $intent = $rq->intent;
        $request = Request::find($rq->id);

        $is_same_status = $request->status_id == $rq->new_status;

        if($intent == "change"){
            $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
            $client = ($rq->client_id) ? Client::find($rq->client_id) : null;

            // price required when sending quote
            if(
                $rq->new_status == 5 &&
                (!$rq->price_code)
            ){
                return back()->with("error", "Uzupełnij wycenę");
            }

            $request->update([
                "client_id" => $rq->client_id,
                "client_name" => $rq->client_name,
                "email" => $rq->email,
                "phone" => $rq->phone,
                "other_medium" => $rq->other_medium,
                "contact_preference" => $rq->contact_preference ?? "email",

                "song_id" => $rq->song_id,
                "quest_type_id" => $rq->quest_type,
                "title" => $rq->title,
                "artist" => $rq->artist,
                "link" => $rq->link,
                "genre_id" => $rq->genre_id,
                "wishes" => $rq->wishes,
                "wishes_quest" => $rq->wishes_quest,

                "price_code" => price_calc($rq->price_code, $rq->client_id, true)["labels"],
                "price" => price_calc($rq->price_code, $rq->client_id, true)["price"],
                "deadline" => $rq->deadline,
                "hard_deadline" => $rq->hard_deadline,
                "delayed_payment" => $rq->delayed_payment,
                "status_id" => $rq->new_status,
            ]);

            // nadpisanie zmienionych gotowców
            if($song){
                $song->update([
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => $rq->link,
                    "genre_id" => $rq->genre_id,
                    "price_code" => preg_replace("/[=\-oyzqr\d]/", "", $rq->price_code),
                    "notes" => $rq->wishes,
                ]);
            }
            if($client){
                $client->update([
                    "client_name" => $rq->client_name,
                    "email" => $rq->email,
                    "phone" => $rq->phone,
                    "other_medium" => $rq->other_medium,
                    "contact_preference" => $rq->contact_preference,
                ]);
            }

            $changes = ($request->price || $request->deadline) ? [
                "cena" => $request->price,
                "termin wykonania" => $request->deadline?->format("d.m.Y"),
            ] : null;
        }else if($intent == "review"){
            //review jako klient
            $request->status_id = $rq->new_status;

            $changes = [];

            if($rq->new_status == 1){
                // $request->price = null;
                // $request->price_code = null;
                $request->deadline = null;
                $request->hard_deadline = null;
                $request->delayed_payment = null;
            }elseif($rq->new_status == 6 && $rq->optbc){
                $changes = ["zmiana ($rq->optbc)" => $request->{$rq->optbc} . " → " . $rq->{"opinion_".$rq->optbc}];
                $request->{$rq->optbc} = $rq->{"opinion_".$rq->optbc};
            }
            $request->save();
        }

        $changed_by = (in_array(Auth::id(), [0, 1], true) && in_array($rq->new_status, [1, 6, 8, 9, 96])) ? $request->client_id : null;
        if($is_same_status){
            $request->changes->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            $this->statusHistory($request->id, $request->status_id, $rq->comment, $changed_by, null, $changes);
        }

        // sending mail
        $flash_content = "Zapytanie gotowe";
        $mailing = null;
        if(in_array($request->status_id, [5, 95])){ // mail do klienta
            if($request->email){
                switch($request->status_id){
                    case 5: Mail::to($request->email)->send(new RequestQuoted($request)); break;
                    case 95: Mail::to($request->email)->send(new Clarification($request)); break;
                }
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($request->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if($request->status_id != 4){ // mail do mnie
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $request->changes->first()->update(["mail_sent" => $mailing]);

        return redirect()->route("request", ["id" => $request->id])->with("success", $flash_content);
    }

    public function requestFinal($id, $status, $with_priority = false){
        $request = Request::find($id);
        if(!$request) abort(404, "Nie ma takiego zapytania");

        if(in_array($request->status_id, [4,7,8,9]))
            return redirect()->route("request", ["id" => $id])->with("error", "Zapytanie już zamknięte");

        $request->status_id = $status;
        $price = price_calc($request->price_code.(($with_priority) ? "z" : ""), $request->client_id, true);

        $is_new_client = 0;

        // adding new quest
        if($status == 9){
            //add new song if not exists
            if(!$request->song_id){
                $song = new Song;
                $song->id = next_song_id($request->quest_type_id);
                $song->title = $request->title;
                $song->artist = $request->artist;
                $song->link = $request->link;
                $song->genre_id = $request->genre_id;
                $song->price_code = preg_replace("/[=\-oyzqr\d]/", "", $request->price_code);
                $song->notes = $request->wishes;
                $song->save();

                $request->song_id = $song->id;
            }
            //add new client if not exists
            if(!$request->client_id){
                $is_new_client = 1;
                $user = new User;
                $user->password = generate_password();
                $user->save();

                $client = new Client;
                $client->id = $user->id;
                $client->client_name = $request->client_name;
                $client->email = $request->email;
                $client->phone = $request->phone;
                $client->other_medium = $request->other_medium;
                $client->contact_preference = $request->contact_preference;
                $client->save();

                $request->client_id = $user->id;

                //bind remaining anonymous quests from the same guy
                Request::whereIn("status_id", [1, 5])
                    ->where("client_name", $request->client_name)
                    ->where("email", $request->email)
                    ->where("phone", $request->phone)
                    ->where("other_medium", $request->other_medium)
                    ->where("contact_preference", $request->contact_preference)
                    ->update(["client_id" => $user->id]);
            }else{
                $client = Client::find($request->client_id);
            }

            $quest = new Quest;
            $quest->id = next_quest_id($request->quest_type_id);
            $quest->song_id = $request->song_id ?? $song->id;
            $quest->client_id = $request->client_id ?? $user->id;
            $quest->status_id = 11;
            $quest->price_code_override = $price["labels"];
            $quest->price = $price["price"];
            $quest->deadline = ($with_priority) ? get_next_working_day() : $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->delayed_payment = $request->delayed_payment;
            $quest->wishes = $request->wishes_quest;
            $quest->save();

            // $invoice = Invoice::create([
            //     "quest_id" => $quest->id,
            //     "amount" => $quest->price
            // ]);

            if($client->budget){
                $sub_amount = min([$request->price, $client->budget]);
                $client->budget -= $sub_amount;
                $this->statusHistory(null, 32, -$sub_amount, $client->id);
                if($sub_amount == $request->price){
                    $quest->paid = true;
                    $quest->save();
                }
                $client->save();
                $this->statusHistory($quest->id, 32, $sub_amount, $client->id);
                // $invoice->update(["paid" => $sub_amount]);
            }

            $request->quest_id = $quest->id;
            $request->price_code = $price["labels"];
            $request->price = $price["price"];
        }

        $request->save();

        //mail do mnie, bo zmiany w zapytaniu
        $mailing = null;
        Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request));
        $mailing = true;

        $this->statusHistory($id, $status, null, (in_array(Auth::id(), [0, 1], true)) ? $request->client_id : null, $mailing);

        if($status == 9){
            //added quest
            $this->statusHistory($request->quest_id, 11, null, $request->client_id);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
        }

        if(in_array(Auth::id(), [0, 1], true)) return redirect()->route("request", ["id" => $request->id]);
        else return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
    }

    public function statusHistory($re_quest_id, $new_status_id, $comment, $changed_by = null, $mailing = null, $changes = null){
        if($re_quest_id){
            $client_id = (strlen($re_quest_id) == 36) ?
                Request::find($re_quest_id)->client_id :
                Quest::find($re_quest_id)->client_id;
        }else{
            $client_id = $changed_by;
        }

        StatusChange::insert([
            "re_quest_id" => $re_quest_id,
            "new_status_id" => $new_status_id,
            "changed_by" => ($client_id == null && in_array($new_status_id, [1, 6, 8, 9, 96])) ? null : $changed_by ?? Auth::id(),
            "comment" => $comment,
            "values" => $changes ? json_encode($changes) : null,
            "mail_sent" => $mailing,
            "date" => now(),
        ]);
    }
    public function questReject(HttpRequest $rq){
        //adding comment
        $history = StatusChange::where("re_quest_id", $rq->id)
            ->where("new_status_id", $rq->status)
            ->first();

        $history->values = json_encode(["powód" => $rq->comment]);
        $history->save();

        $where_to = (!Auth::check()) ? "home" : "dashboard";
        return redirect()->route($where_to)->with("success", "Komentarz dodany");
    }

    public function quests(HttpRequest $rq){
        $client_id = $rq->client;
        $status_id = $rq->status;
        $paid = $rq->paid;

        $client = Client::find($client_id) ?? Auth::user()->client;
        if($client_id && $client_id != Auth::id() && !in_array(Auth::id(), [0, 1], true)) abort(403, "Widok niedostępny");

        $quests = Quest::orderBy("quests.created_at", "desc");
        if($client){ $quests = $quests->where("client_id", $client->id); }
        if($status_id) $quests = $quests->where("status_id", $status_id);
        if($paid) $quests = $quests->where("paid", $paid);
        $quests = $quests->paginate(25);

        return view(user_role().".quests", [
            "title" => $client_id ? "$client->client_name – zlecenia" : "Lista zleceń",
            "quests" => $quests
        ]);
    }

    public function quest($id){
        $quest = Quest::find($id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");

        $prices = DB::table("prices")
            ->where("quest_type_id", $quest->song->type->id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        if($quest->client_id != Auth::id() && !in_array(Auth::id(), [0, 1], true)) abort(403, "To nie jest Twoje zlecenie");

        $files_raw = collect(Storage::files('safe/'.$quest->song_id))
            ->sortByDesc(function($file){return Storage::lastModified($file);});
        $files = []; $last_mod = []; $desc = [];

        if(!empty($files_raw)){
            foreach($files_raw as $file){
                $name = [];
                $name_raw = pathinfo($file, PATHINFO_FILENAME);
                preg_match("/_(.*)$/", $name_raw, $matches);
                if(!empty($matches)){
                    $name[2] = $matches[1];
                    $name_raw = str_replace("_".$name[2], "", $name_raw);
                }
                preg_match("/=(.*)$/", $name_raw, $matches);
                if(!empty($matches)){
                    $name[1] = $matches[1];
                    $name_raw = str_replace("=".$name[1], "", $name_raw);
                }
                $name[0] = $name_raw;

                if(!isset($name[1])) $name[1] = "podstawowy";
                if(!isset($name[2])) $name[2] = "wersja główna";
                $files[$name[0]][$name[1]][$name[2]][] = $file;
                $last_mod[$name[1]][$name[2]] = Carbon::parse(Storage::lastModified($file));
                if(pathinfo($file, PATHINFO_EXTENSION) == "md") $desc[$name[0]][$name[1]][$name[2]] = $file;
            }
        }

        return view(
            user_role().".quest",
            array_merge(
                ["title" => "Zlecenie"],
                compact("quest", "prices", "files", "last_mod", "desc"),
                (isset($stats_statuses) ? compact("stats_statuses") : []),
            )
        );
    }

    public function modQuestBack(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->quest_id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        if(SongWorkTime::where(["song_id" => $quest->song_id, "now_working" => 1])->first()){
            return back()->with("error", "Zatrzymaj zegar");
        }

        // wpisywanie wpłaty za zlecenie
        if($rq->status_id == 32){
            if(empty($rq->comment)) return redirect()->route("quest", ["id" => $rq->quest_id])->with("error", "Nie podałeś ceny");

            // opłacenie zlecenia (sprawdzenie nadwyżki)
            $amount_to_pay = $quest->price - $quest->payments->sum("comment");
            $amount_for_budget = $rq->comment - $amount_to_pay;

            $this->statusHistory($rq->quest_id, $rq->status_id, min($rq->comment, $amount_to_pay), $quest->client_id);
            if($amount_for_budget > 0){
                $this->statusHistory(null, $rq->status_id, $amount_for_budget, $quest->client_id);

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
                    Mail::to($quest->client->email)->send(new PaymentReceived($quest));
                    StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => true]);
                    $flash_content .= ", mail wysłany";
                }
                if($quest->client->contact_preference != "email"){
                    // StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => false]);
                    $flash_content .= ", ale wyślij wiadomość";
                }
            }

            return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
        }

        $is_same_status = $quest->status_id == $rq->status_id;
        $quest->status_id = $rq->status_id;
        $quest->save();

        if($is_same_status){
            $quest->changes->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            $this->statusHistory(
                $rq->quest_id,
                $rq->status_id,
                $rq->comment,
                (in_array(Auth::id(), [0, 1], true) && in_array($rq->status_id, [16, 18, 19, 26, 96])) ? $quest->client_id : null
            );
        }

        // sending mail
        $flash_content = "Faza zmieniona";
        $mailing = null;
        if(in_array($quest->status_id, [15, 95])){ // mail do klienta
            if($quest->client->email){
                switch($quest->status_id){
                    case 15: Mail::to($quest->client->email)->send(new QuestUpdated($quest)); break;
                    case 95: Mail::to($quest->client->email)->send(new Clarification($quest)); break;
                }
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($quest->client->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if(!in_array(Auth::id(), [0, 1], true)){ // mail do mnie
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($quest));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $quest->changes->first()->update(["mail_sent" => $mailing]);


        return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
    }
    public function questSongUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $song = Song::find($rq->id);
        if(!$song) abort(404, "Nie ma takiego utworu");
        $song->update([
            "title" => $rq->title,
            "artist" => $rq->artist,
            "link" => $rq->link,
            "notes" => $rq->wishes,
        ]);
        return back()->with("success", "Utwór zmodyfikowany");
    }
    public function questWishesUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        $quest->update([
            "wishes" => $rq->wishes_quest,
        ]);
        return back()->with("success", "Zlecenie zmodyfikowane");
    }
    public function questQuoteUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        $price_before = $quest->price;
        $deadline_before = $quest->deadline;
        $delayed_payment_before = $quest->delayed_payment;
        $quest->update([
            "price_code_override" => $rq->price_code_override,
            "price" => price_calc($rq->price_code_override, $quest->client_id)["price"],
            "paid" => ($quest->payments->sum("comment") >= price_calc($rq->price_code_override, $quest->client_id)["price"]),
            "deadline" => $rq->deadline,
            "delayed_payment" => $rq->delayed_payment,
        ]);
        $difference = $quest->price - $price_before;
        if($quest->client->budget){
            $sub_amount = min([$difference, $quest->client->budget]);
            $quest->client->budget -= $sub_amount;
            $this->statusHistory(null, 32, -$sub_amount, $quest->client->id);
            if($sub_amount == $difference){
                $quest->paid = true;
                $quest->save();
            }
            $quest->client->save();
            $this->statusHistory($quest->id, 32, $sub_amount, $quest->client->id);
        }

        // if($price_before != $quest->price){
        //     Invoice::create([
        //         "quest_id" => $quest->id,
        //         "amount" => $quest->price - $price_before,
        //         "primary" => false,
        //     ]);
        // }

        // sending mail
        $mailing = null;
        if($quest->client->email){
            Mail::to($quest->client->email)->send(new QuestRequoted($quest, $rq->reason, $difference));
            $mailing = true;
        }
        if($quest->client->contact_preference != "email"){
            $mailing ??= false;
        }

        // zbierz zmiany
        $changes = [];
        foreach([
            "cena" => [$price_before, $quest->price],
            "termin oddania pierwszej wersji" => [$deadline_before->format("Y-m-d"), $quest->deadline->format("Y-m-d")],
            "opóźnienie wpłaty" => [$delayed_payment_before?->format("Y-m-d"), $quest->delayed_payment?->format("Y-m-d")],
        ] as $attr => $value){
            if ($value[0] != $value[1]) $changes[$attr] = $value[0] . " → " . $value[1];
        }
        $changes["zmiana z uwagi na"] = $rq->reason;

        app("App\Http\Controllers\BackController")->statusHistory(
            $rq->id,
            31,
            null,
            null,
            $mailing,
            $changes
        );
        return back()->with("success", "Wycena zapytania zmodyfikowana");
    }

    public function setPatronLevel($client_id, $level){
        if(Auth::id() === 0) return redirect()->route("dashboard")->with("error", OBSERVER_ERROR());
        $client = Client::findOrFail($client_id);

        $client->update(["helped_showcasing" => $level]);
        $mailing = false;
        if($level == 0 && $client->email){
            Mail::to($client->email)->send(new PatronRejected($client));
            $mailing = true;
        }

        if(Auth::id() == 1) return redirect()->route("dashboard")->with("success", (($level == 2) ? "Wniosek przyjęty" : "Wniosek odrzucony").($mailing ? ", mail wysłany" : ""));
        return redirect()->route("dashboard")->with("success", "Wystawienie opinii odnotowane");
    }

    public function workClock(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $now_working = SongWorkTime::where("now_working", 1)->first();

        if($now_working){
            $now_working->update([
                "now_working" => 0,
                "time_spent" => Carbon::createFromTimeString($now_working->time_spent)
                        ->addSeconds(Carbon::createFromTimeString($now_working->since)->diffInSeconds(now()))
                        ->format("H:i:s")
            ]);
        }

        if($rq->status_id != 13){
            SongWorkTime::updateOrCreate([
                "status_id" => $rq->status_id,
                "song_id" => $rq->song_id,
            ],
            [
                "now_working" => 1,
                "since" => now(),
            ]);
        }

        return back()->with("success", "Praca zalogowana");
    }
    public function workClockRemove($song_id, $status_id){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        SongWorkTime::where("song_id", $song_id)->where("status_id", $status_id)->first()->delete();
        return back()->with("success", "Wpis pracy usunięty");
    }

    public function songs(HttpRequest $rq){
        $search = strtolower($rq->search ?? "");
        $songs = Song::orderBy("title")
            ->whereRaw("LOWER(title) like '%$search%'")
            ->orWhereRaw("LOWER(artist) like '%$search%'")
            ->orWhereRaw("LOWER(id) like '%$search%'")
            ->paginate();

        $song_work_times = [];
        $price_codes = [];
        foreach($songs as $song){
            $price_codes[$song->id] = [];
            for($i = 0; $i < strlen($song->price_code); $i++){
                $letter = $song->price_code[$i];
                if(preg_match('/[a-z]/', $song->price_code[$i])){
                    $price_codes[$song->id][] = DB::table("prices")->where("indicator", $letter)->value("service");
                }
            }
            $price_codes[$song->id] = implode("<br>", $price_codes[$song->id]);

            $song_work_times[$song->id] = [
                "total" => gmdate("H:i:s", DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->sum(DB::raw("TIME_TO_SEC(time_spent)"))),
                "parts" => DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->get()
                    ->toArray()
            ];
            $song_work_times[$song->id]["parts"] = implode("<br>", array_map(function($x){
                return Status::find($x->status_id)->status_name . " → " . $x->time_spent;
            }, $song_work_times[$song->id]["parts"]));
        }

        return view(user_role().".songs", array_merge(
            ["title" => "Lista utworów"],
            compact("songs", "song_work_times", "price_codes", "search")
        ));
    }

    public function showcases(){
        $showcases = Showcase::orderBy("updated_at", "desc")->paginate(10);

        $songs_raw = Song::whereDoesntHave('showcase')
            ->whereHas('quests', function($q){
                $q->where('status_id', 19);
            })
            ->orderByDesc("created_at")
            ;

        $potential_showcases = clone $songs_raw;
        $potential_showcases = $potential_showcases->whereDate('created_at', '>', Carbon::today()->subMonth()->format("Y-m-d H:i:s"))->get();

        $songs_raw = $songs_raw->get()->toArray();
        foreach($songs_raw as $song){
            $songs[$song["id"]] = "$song[title] ($song[artist]) [$song[id]]";
        }

        return view(user_role().".showcases", array_merge(
            ["title" => "Lista reklam"],
            compact("showcases", "songs", "potential_showcases")
        ));
    }

    public function addShowcase(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        Showcase::create([
            "song_id" => $rq->song_id,
            "link_fb" => (filter_var($rq->link_fb, FILTER_VALIDATE_URL)) ?
                "<a target='_blank' href='$rq->link_fb'>$rq->link_fb</a>" : $rq->link_fb,
            "link_ig" => $rq->link_ig,
        ]);

        return back()->with("success", "Dodano pozycję");
    }

    public function ppp($page = "0-index"){
        $titles = [];
        foreach(File::allFiles(resource_path("views/doc")) as $key => $ttl){
            $titles[$key] = preg_replace('/(.*)doc[\/\\\](.*)\.blade\.php/', "$2", $ttl);
        }

        return view(user_role().".ppp", array_merge(
            ["title" => "Poradnik Przyszłych Pokoleń"],
            compact("page", "titles")
        ));
    }

    public function settings(){
        $settings = DB::table("settings")->get();

        return view(user_role().".settings", array_merge(
            ["title" => "Ustawienia"],
            compact("settings")
        ));
    }
}
