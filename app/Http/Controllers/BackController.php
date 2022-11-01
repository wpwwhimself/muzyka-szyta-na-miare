<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;
        $archmage = (Auth::id() == 1) ? "archmage." : "";

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

        return view($archmage."dashboard", [
            "title" => (Auth::id() == 1) ? "Szpica arcymaga" : "Pulpit",
            "quests" => $quests,
            "requests" => $requests
        ]);
    }

    public function quests(){
        $client = Auth::user()->client;
        $archmage = (Auth::id() == 1) ? "archmage." : "";

        $quests = Quest::orderBy("quests.created_at", "desc");
        if(Auth::id() != 1){
            $quests = $quests->where("client_id", $client->id);
        }
        $quests = $quests->get();

        return view($archmage."quests", [
            "title" => "Lista zleceń",
            "quests" => $quests
        ]);
    }
    public function requests(){
        $client = Auth::user()->client;
        $archmage = (Auth::id() == 1) ? "archmage." : "";

        $requests = Request::where("status_id", "!=", 9)
            ->orderBy("created_at", "desc");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
        }
        $requests = $requests->get();

        return view($archmage."requests", [
            "title" => "Lista zapytań",
            "requests" => $requests
        ]);
    }

    public function quest(){
        $quest = [];
        $archmage = (Auth::id() == 1) ? "archmage." : "";

        return view($archmage."quest", [
            "title" => "Zlecenie",
            "quest" => $quest
        ]);
    }
    public function request($id){
        $request = Request::find($id);
        $archmage = (Auth::id() == 1) ? "archmage." : "";

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"];
            }
        }else{
            $clients = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->pluck("service", "indicator")->toArray();

        return view($archmage."request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients")));
    }

    public function addRequest(){
        $archmage = (Auth::id() == 1) ? "archmage." : "";

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"];
            }
        }else{
            $clients = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->pluck("service", "indicator")->toArray();

        return view($archmage."add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients")));
    }
    public function addRequestBack(HttpRequest $rq){
        $request = new Request;

        if(Auth::id() != 1){
            $request->client_id = Auth::user()->client->id;
            $request->client_name = Auth::user()->client->client_name;
            $request->email = Auth::user()->client->email;
            $request->phone = Auth::user()->client->phone;
            $request->other_medium = Auth::user()->client->other_medium;
            $request->contact_preference = Auth::user()->client->contact_preference ?? "email";
            $request->wishes = Auth::user()->client->default_wishes;
            $request->hard_deadline = true;
        }else{
            if($rq->client_id){
                $client = Client::find($rq->client_id);
                $request->client_id = $rq->client_id;
                $request->client_name = $client->client_name;
                $request->email = $client->email;
                $request->phone = $client->phone;
                $request->other_medium = $client->other_medium;
                $request->contact_preference = $client->contact_preference ?? "email";
            }else{
                $request->client_name = $rq->client_name;
                $request->email = $rq->email;
                $request->phone = $rq->phone;
                $request->other_medium = $rq->other_medium;
                $request->contact_preference = $rq->contact_preference ?? "email";
            }
            $request->hard_deadline = ($rq->hard_deadline == "on");
        }
        $request->title = $rq->title;
        $request->artist = $rq->artist;
        $request->cover_artist = $rq->cover_artist;
        $request->link = $rq->link;
        $request->price = $rq->price;
        $request->deadline = $rq->deadline;

        $request->wishes = ($request->wishes == null) ? $rq->wishes : $request->wishes."\n".$rq->wishes;

        $request->quest_type_id = $rq->quest_type;
        $request->made_by_me = true;
        $request->status_id = (Auth::id() == 1) ? 5 : 1;
        $request->save();

        return redirect("requests")->with("success", "Dodano nowe zapytanie");
    }
}
