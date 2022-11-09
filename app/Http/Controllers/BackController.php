<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\StatusChange;
use Illuminate\Http\Request as HttpRequest;
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
        $quests = $quests->get();

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
        $requests = $requests->get();

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests
        ]);
    }

    public function quest(){
        $quest = [];

        return view(user_role().".quest", [
            "title" => "Zlecenie",
            "quest" => $quest
        ]);
    }
    public function request($id){
        $request = Request::find($id);

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"];
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $artist = ($song["cover_artist"] != null) ? $song["cover_artist"] : $song["artist"];
                $songs[$song["id"]] = $song["title"]." ($artist)";
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

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs")));
    }

    public function addRequest(){
        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"];
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $artist = ($song["cover_artist"] != null) ? $song["cover_artist"] : $song["artist"];
                $songs[$song["id"]] = $song["title"]." ($artist)";
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

        return view(user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs")));
    }
    public function modRequestBack(HttpRequest $rq){
        $modifying = $rq->modifying; //insert -- 0; update -- request_id
        $request = ($modifying != 0) ? Request::find($modifying) : new Request;

        if(Auth::id() != 1){
            // składanie requesta przez klienta
            if(Auth::check()){
                $request->client_id = Auth::user()->client->id;
            }
            $request->made_by_me = false;
            $request->quest_type_id = $rq->quest_type;
            $request->title = $rq->title;
            $request->artist = $rq->artist;
            $request->cover_artist = $rq->cover_artist;
            $request->link = $rq->link;
            $request->wishes = $rq->wishes;
        }else{
            // składanie requesta przeze mnie
            $request->made_by_me = true;
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
                $request->quest_type_id = $song->quest_type_id;
                $request->title = $song->title;
                $request->artist = $song->artist;
                $request->cover_artist = $song->cover_artist;
                $request->link = $song->link;
                $request->wishes = $song->wishes;
            }else{
                $request->quest_type_id = $rq->quest_type;
                $request->title = $rq->title;
                $request->artist = $rq->artist;
                $request->cover_artist = $rq->cover_artist;
                $request->link = $rq->link;
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

        $this->statusHistory($request->id, $request->status_id, null);

        return redirect()->route("request", ["id" => $request->id])->with("success", "Zapytanie gotowe");
    }

    public function requestFinal($id, $status){
        $request = Request::findOrFail($id);

        $request->status_id = $status;
        //TODO if($status == 9) utwórz questa i dopisz go do requesta
        $request->save();

        $this->statusHistory($id, $status, null); //todo komentarze

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
