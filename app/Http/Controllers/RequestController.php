<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageQuestMod;
use App\Mail\Clarification;
use App\Mail\Onboarding;
use App\Mail\RequestQuoted;
use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function list(HttpRequest $rq){
        $client = Auth::user()->client;
        $client_id = $rq->client;
        $client_name = $rq->client_name;
        $status_id = $rq->status;

        $requests = Request::orderBy("updated_at", "desc");
        if(!is_archmage()){ $requests = $requests->where("client_id", $client->id); }
        if($client_id) $requests = $requests->where("client_id", $client_id);
        if($client_name) $requests = $requests->where("client_name", "regexp", $client_name);
        if($status_id) $requests = $requests->where("status_id", $status_id);
        $requests = $requests->paginate(25);

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }

    public function show($id){
        $request = Request::find($id);
        if(!$request) abort(404, "Nie ma takiego zapytania");
        $pad_size = 30; // used by dropdowns for mobile preview fix

        if(is_archmage()){
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

        $warnings = is_archmage() ? [
            'song' => [
                'Klient ma domyślne życzenia' => $request->client?->default_wishes,
                'Klient ma deadline' => $request->hard_deadline,
            ],
        ] : [
            "quote" => [
                'Zwróć uwagę, kiedy masz zapłacić' => $request->delayed_payment,
                'Wycena nadal w przygotowaniu' => !$request->price,
                'Wycena może być nieaktualna' => $request->price && $request->status_id == 1,
            ],
        ];

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres", "warnings")));
    }

    public function add(HttpRequest $rq){
        $pad_size = 24; // used by dropdowns for mobile preview fix

        if (is_archmage()) {
            // arcymag od razu tworzy pusty request i edytuje go później
            $client_data = [];
            if ($rq->has("client")) $client_data["client_id"] = $rq->client;
            if ($rq->has("client_new")) {
                $client_data = array_filter(
                    array_combine(
                        [
                            "client_name",
                            "email",
                            "phone",
                            "other_medium",
                            "contact_preference",
                        ],
                        explode("*", $rq->client_new)
                    ),
                    fn($el) => $el != ""
                );
            }

            $request = Request::create(array_merge(
                ["status_id" => 1],
                $client_data
            ));
            BackController::newStatusLog($request->id, 1, "~ spoza strony");
            return redirect()->route("request", ["id" => $request->id])->with("success", "Szablon zapytania gotowy");
        }

        $clients = [];
        $songs = [];

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

    public function finalized($id, $status, $is_new_client){
        $request = Request::findOrFail($id);

        return view("request-finalized", array_merge(
            ["title" => "Zapytanie zamknięte"],
            compact("id", "status", "is_new_client", "request")
        ));
    }

    ////////////////////////////////////////////////////

    public function processAdd(HttpRequest $rq){
        if(isset($rq->m_test) && $rq->m_test != 20) return redirect()->route("home")->with("error", "Cztery razy pięć nie równa się $rq->m_test");
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());

        $flash_content = "Zapytania dodane";
        $loop_length = is_array($rq->quest_type) ? count($rq->quest_type) : 1;
        $requests_created = [];

        for($i = 0; $i < $loop_length; $i++){
            if(is_archmage()){ // arcymag
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
                    "link" => yt_cleanup($rq->link),
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
                        "link" => yt_cleanup($rq->link),
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

                if($rq->new_status == 5) BackController::newStatusLog($request->id, 1, null);
                BackController::newStatusLog($request->id, $rq->new_status, $rq->comment);

                //mailing
                $mailing = null;
                if(
                    $request->email &&
                    $rq->new_status == 5
                ){
                    Mail::to($request->email)->send(new RequestQuoted($request->fresh()));
                    $mailing = true;
                    $flash_content .= ", mail wysłany";
                }
                if($request->contact_preference != "email"){
                    $mailing ??= false;
                    $flash_content .= ", ale wyślij wiadomość";
                }
                if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);
            }else{ // klient
                //bulk

                // ignore empty requests
                if (empty($rq->title[$i]) && empty($rq->artist[$i]) && empty($rq->link[$i])) {
                    continue;
                }

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
                    "link" => yt_cleanup($rq->link[$i]),
                    "wishes" => $rq->wishes[$i],

                    "hard_deadline" => $rq->hard_deadline[$i],
                    "status_id" => $rq->new_status,
                ]);

                //mailing do mnie
                $mailing = null;
                Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
                $mailing = true;

                BackController::newStatusLog($request->id, $rq->new_status, $rq->wishes[$i], (Auth::check()) ? Auth::id() : null, $mailing);
            }
            $requests_created[] = $request;
        }

        if(Auth::check()) return redirect()->route("dashboard")->with("success", $flash_content);
        return view("client.request-confirm", array_merge(
            ["title" => "Zapytanie dodane"],
            compact("requests_created")
        ))->with("success", $flash_content);
    }

    public function processMod(HttpRequest $rq){
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
                "link" => yt_cleanup($rq->link),
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
                    "link" => yt_cleanup($rq->link),
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

        $changed_by = (is_archmage() && in_array($rq->new_status, [1, 6, 8, 9, 96])) ? $request->client_id : null;
        if($is_same_status){
            $request->history->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            BackController::newStatusLog($request->id, $request->status_id, $rq->comment, $changed_by, null, $changes);
        }

        // sending mail
        $flash_content = "Zapytanie gotowe";
        $mailing = null;
        if(in_array($request->status_id, [5, 95])){ // mail do klienta
            if($request->email){
                switch($request->status_id){
                    case 5: Mail::to($request->email)->send(new RequestQuoted($request->fresh())); break;
                    case 95: Mail::to($request->email)->send(new Clarification($request->fresh())); break;
                }
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($request->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if($request->status_id != 4){ // mail do mnie
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);

        return redirect()->route("request", ["id" => $request->id])->with("success", $flash_content);
    }

    public function finalize($id, $status, $with_priority = false){
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
                $song->link = yt_cleanup($request->link);
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
                BackController::newStatusLog(null, 32, -$sub_amount, $client->id);
                if($sub_amount == $request->price){
                    $quest->paid = true;
                    $quest->save();
                }
                $client->save();
                BackController::newStatusLog($quest->id, 32, $sub_amount, $client->id);
                // $invoice->update(["paid" => $sub_amount]);
            }

            $request->quest_id = $quest->id;
            $request->price_code = $price["labels"];
            $request->price = $price["price"];
        }

        $request->save();

        //mail do mnie, bo zmiany w zapytaniu
        $mailing = null;
        Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
        $mailing = true;

        BackController::newStatusLog($id, $status, null, (is_archmage()) ? $request->client_id : null, $mailing);

        if($status == 9){
            //added quest
            BackController::newStatusLog($request->quest_id, 11, null, $request->client_id);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
            //send onboarding if new client
            if($request->client->email && $is_new_client){
                Mail::to($request->client->email)->send(new Onboarding($request->client));
            }
        }

        if(is_archmage()) return redirect()->route("request", ["id" => $request->id]);
        else return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
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
}
