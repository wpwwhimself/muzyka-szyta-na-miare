<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        }
        $requests = $requests->get();
        $quests = $quests->get();

        return view(user_role().".dashboard", [
            "title" => (Auth::id() == 1) ? "Szpica arcymaga" : "Pulpit",
            "quests" => $quests,
            "requests" => $requests
        ]);
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

    public function quest($id){
        $quest = Quest::findOrFail($id);

        $history = DB::table("status_changes")->whereIn("re_quest_id", [$id, Request::where("quest_id", $id)->value("id")])->get();

        return view(user_role().".quest", array_merge(["title" => "Zlecenie"], compact("quest", "history")));
    }
    public function request($id){
        $request = Request::findOrFail($id);

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"];
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
                $clients[$client["id"]] = $client["client_name"];
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
            }
            if($request->made_by_me === null) $request->made_by_me = false;
            $request->quest_type_id = $rq->quest_type;
            $request->title = $rq->title;
            $request->artist = $rq->artist;
            $request->link = $rq->link;
            $request->wishes = $rq->wishes;
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
        $request->hard_deadline = $rq->hard_deadline;

        if($rq->questioning) $request->status_id = 6;
        else $request->status_id = (Auth::id() == 1) ? 5 : 1;

        $request->save();

        $comment = null;
        if(in_array($request->status_id, [5, 6])){
            $comment = json_encode(Arr::where($request->getChanges(), fn($val, $key) => ($key != "updated_at" && $key != "status_id")));
            if($comment == "[]") $comment = null;
        }

        $this->statusHistory($request->id, $request->status_id, $comment);

        return redirect()->route("request", ["id" => $request->id])->with("success", "Zapytanie gotowe");
    }

    public function requestFinal($id, $status){
        $request = Request::findOrFail($id);

        $request->status_id = $status;

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
            if($client->budget >= $request->price){
                $quest->paid = 1;
                $client->budget -= $request->price;
                $client->save();
            }else{
                $quest->paid = 0;
            }
            $quest->deadline = $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->save();

            $request->quest_id = $quest->id;
        }

        $request->save();

        $this->statusHistory($id, $status, null);

        if(!Auth::check()){
            return redirect()->route("request-finalized", ["status" => $status]);
        }
        return redirect()->route("requests")->with("success", "Zapytanie zmienione");
    }

    public function statusHistory($re_quest_id, $new_status_id, $comment){
        $row = new StatusChange;

        $row->re_quest_id = $re_quest_id;
        $row->new_status_id = $new_status_id;
        $row->changed_by = Auth::id();
        $row->comment = $comment;

        $row->save();
    }
}
