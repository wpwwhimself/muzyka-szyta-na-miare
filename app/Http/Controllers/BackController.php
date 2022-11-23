<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

        $requests = Request::whereNotIn("status_id", [7, 8, 9])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        $quests = Quest::whereNotIn("status_id", [18, 19])
            ->orderBy("status_id")
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
            $quests = $quests->where("client_id", $client->id);
            $quests_total = client_exp(Auth::id());
        }else{
            $patrons_adepts = Client::where("helped_showcasing", 1)->get();
        }
        $requests = $requests->get();
        $quests = $quests->get();

        return view(user_role().".dashboard", array_merge(
            ["title" => (Auth::id() == 1) ? "Szpica arcymaga" : "Pulpit"],
            compact("quests", "requests"),
            (isset($quests_total) ? compact("quests_total") : []),
            (isset($patrons_adepts) ? compact("patrons_adepts") : [])
        ));
    }

    public function clients(){
        $clients_raw = Client::all();
        $clients_count = count($clients_raw);
        $max_exp = 0;
        $classes = ["Weterani", "Biegli", "Zainteresowani", "Nowicjusze", "Debiutanci"];

        foreach($clients_raw as $client){
            $client->exp = client_exp($client->id);
            if($client->exp > $max_exp) $max_exp = $client->exp;

            if(is_veteran($client->id)) $class = $classes[0];
            elseif(client_exp($client->id) >= 4) $class = $classes[1];
            elseif(client_exp($client->id) >= 2) $class = $classes[2];
            elseif(client_exp($client->id) >= 1) $class = $classes[3];
            else $class = $classes[4];

            $clients[$class][] = $client;
        }
        ksort($clients);
        foreach($clients as $k => $v){
            $clients[$k] = collect($v)->sortBy([['exp', "desc"], ['client_name', 'asc']]);
        }

        return view(user_role().".clients", array_merge(
            ["title" => "Klienci"],
            compact("clients", "max_exp","classes", "clients_count")
        ));
    }

    public function requests(){
        $client = Auth::user()->client;

        $requests = Request::orderBy("updated_at", "desc");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
        }
        $requests = $requests->paginate(25);

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }
    public function request($id){
        $request = Request::findOrFail($id);

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"] ." (". ($client["email"] ?? $client["phone"]) .")";
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = "$song[title] ($song[artist])";
            }
        }else{
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres")));
    }
    public function addRequest(){
        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"] ." (". ($client["email"] ?? $client["phone"]) .")";
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = "$song[title] ($song[artist])";
            }
        }else{
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->orderBy("quest_type_id")->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs", "genres")));
    }
    public function modRequestBack(HttpRequest $rq){
        $modifying = $rq->modifying; //insert -- 0; update -- request_id
        $request = ($modifying != 0) ? Request::find($modifying) : new Request;

        if(Auth::id() != 1){
            // składanie requesta przez klienta
            if(Auth::check()){
                $request->client_id = Auth::user()->client->id;
            }else if(!$modifying){
                if($rq->m_test != 20) return redirect()->route("home")->with("error", "Cztery razy pięć nie równa się $rq->m_test");
                $request->client_name = $rq->client_name;
                $request->email = $rq->email;
                $request->phone = $rq->phone;
                $request->other_medium = $rq->other_medium;
                $request->contact_preference = $rq->contact_preference;
            }
            if($request->made_by_me === null) $request->made_by_me = false;
            $request->quest_type_id = $rq->quest_type;
            $request->title = $rq->title;
            $request->artist = $rq->artist;
            $request->link = $rq->link;
            $request->wishes = $rq->wishes;
            $request->hard_deadline = $rq->hard_deadline;
        }else{
            // składanie requesta przeze mnie
            if($request->made_by_me === null) $request->made_by_me = true;
            if($rq->client_id){
                $request->client_id = $rq->client_id;
                $client = Client::find($rq->client_id);
                $request->client_name = $client->client_name;
                $request->email = $client->email;
                $request->phone = $client->phone;
                $request->other_medium = $client->other_medium;
                $request->contact_preference = $client->contact_preference;
            }else{
                $request->client_name = $rq->client_name;
                $request->email = $rq->email;
                $request->phone = $rq->phone;
                $request->other_medium = $rq->other_medium;
                $request->contact_preference = $rq->contact_preference ?? "email";
            }
            if($rq->bind_with_song == "on"){
                $request->song_id = $rq->song_id;
                $song = Song::find($rq->song_id);
                $request->quest_type_id = song_quest_type($rq->song_id)->id;
                $request->title = $song->title;
                $request->artist = $song->artist;
                $request->link = $song->link;
                $request->genre_id = $song->genre_id;
                $request->wishes = $song->wishes;
            }else{
                $request->quest_type_id = $rq->quest_type;
                $request->title = $rq->title;
                $request->artist = $rq->artist;
                $request->link = $rq->link;
                $request->genre_id = $rq->genre_id;
                $request->wishes = $rq->wishes;
            }
            $request->price_code = $rq->price_code;
            $request->price = price_calc($rq->price_code, $rq->client_id)[0];
        }
        $request->deadline = $rq->deadline;

        if($rq->questioning) $request->status_id = 6;
        else $request->status_id = (Auth::id() == 1) ? 5 : 1;

        //zbierz zmiany przed dodaniem
        $comment = null;
        if(in_array($request->status_id, [5, 6]) && $modifying){
            $changes = [];
            $keys = array_keys(Arr::except($request->getDirty(), ["updated_at", "status_id"]));
            $pre = $request->getOriginal();
            $post = $request->getDirty();
            foreach($keys as $key){
                $changes[$key] = $pre[$key] . " → " . $post[$key];
            }
            $comment = json_encode($changes);
            if($comment == "[]") $comment = null;
        }

        $request->save();

        $this->statusHistory($request->id, $request->status_id, $comment);

        return redirect()->route("request", ["id" => $request->id])->with("success", "Zapytanie gotowe");
    }

    public function requestFinal($id, $status){
        $request = Request::findOrFail($id);

        $request->status_id = $status;

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
                $song->price_code = $request->price_code;
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
            }else{
                $client = Client::find($request->client_id);
            }

            $quest = new Quest;
            $quest->id = next_quest_id($request->quest_type_id);
            $quest->song_id = $request->song_id ?? $song->id;
            $quest->client_id = $request->client_id ?? $user->id;
            $quest->status_id = 11;
            $quest->price_code_override = $request->price_code;
            $quest->price = $request->price;
            $quest->deadline = $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->save();
            if($client->budget >= $request->price){
                $client->budget -= $request->price;
                $client->save();
                Payment::insert([
                    "client_id" => $client->id,
                    "quest_id" => $quest->id,
                    "payment" => $request->price
                ]);
            }

            $request->quest_id = $quest->id;
        }

        $request->save();

        $this->statusHistory($id, $status, null);
        if($status == 9){
            //added song
            $this->statusHistory($request->quest_id, 11, null);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
        }

        return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
    }

    public function statusHistory($re_quest_id, $new_status_id, $comment){
        $row = new StatusChange;

        $row->re_quest_id = $re_quest_id;
        $row->new_status_id = $new_status_id;
        $row->changed_by = (in_array($new_status_id, [11])) ? 1 : Auth::id();
        $row->comment = $comment;
        $row->date = now();

        $row->save();
    }
    public function questReject(HttpRequest $rq){
        //adding comment
        $history = StatusChange::where("re_quest_id", $rq->id)
            ->where("new_status_id", $rq->status)
            ->first();

        $history->comment = json_encode(["powód" => $rq->comment]);
        $history->save();

        $where_to = (!Auth::check()) ? "home" : "dashboard";
        return redirect()->route($where_to)->with("success", "Komentarz dodany");
    }

    public function quests(){
        $client = Auth::user()->client;

        $quests = Quest::orderBy("quests.created_at", "desc");
        if(Auth::id() != 1){
            $quests = $quests->where("client_id", $client->id);
        }
        $quests = $quests->paginate(25);

        return view(user_role().".quests", [
            "title" => "Lista zleceń",
            "quests" => $quests
        ]);
    }
    public function quest($id){
        $quest = Quest::findOrFail($id);

        $prices = DB::table("prices")->orderBy("quest_type_id")->pluck("service", "indicator")->toArray();
        if(Auth::id() == 1) $stats_statuses = DB::table("statuses")->where("id", ">=", 100)->get()->toArray();
        //TODO historia tworzenia

        return view(
            user_role().".quest",
            array_merge(
                ["title" => "Zlecenie"],
                compact("quest", "prices"),
                compact("stats_statuses") ?? []
            )
        );
    }
    public function modQuestBack(HttpRequest $rq){
        $quest = Quest::findOrFail($rq->quest_id);

        // wpisywanie wpłaty za zlecenie
        if($rq->status_id == 32){
            if(empty($rq->comment)) return redirect()->route("quest", ["id" => $rq->quest_id])->with("error", "Nie podałeś ceny");
            $this->statusHistory($rq->quest_id, $rq->status_id, $rq->comment);
            return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", "Cena wpisana");
        }

        $quest->status_id = $rq->status_id;
        $quest->save();

        $this->statusHistory($rq->quest_id, $rq->status_id, $rq->comment);
        
        return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", "Faza zmieniona");
    }
}
