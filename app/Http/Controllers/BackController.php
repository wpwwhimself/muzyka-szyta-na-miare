<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

        $requests = Request::leftJoin("clients", "requests.client_id", "=", "clients.id")
            ->leftJoin("statuses", "requests.status_id", "=", "statuses.id")
            ->whereNotIn("status_id", [7, 8, 9])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        $quests = Quest::leftJoin("songs", "quests.song_id", "=", "songs.id")
            ->leftJoin("clients", "quests.client_id", "=", "clients.id")
            ->leftJoin("statuses", "quests.status_id", "=", "statuses.id")
            ->whereNotIn("status_id", [18, 19])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
            $quests = $quests->where("client_id", $client->id);
        }
        $requests = $requests->get([
            "requests.id",
            "title",
            "artist",
            "requests.client_name as rq_client_name",
            "clients.client_name as cl_client_name",
            "status_id",
            "status_name",
            "price",
            "deadline",
            "hard_deadline"
        ]);
        $quests = $quests->get([
            "quests.id",
            "title",
            "artist",
            "client_name",
            "status_id",
            "status_name",
            "price",
            "paid",
            "deadline",
            "hard_deadline"
        ]);

        return view("dashboard", [
            "title" => "Podsumowanie",
            "quests" => $quests,
            "requests" => $requests
        ]);
    }

    public function quests(){
        $client = Auth::user()->client;

        $quests = Quest::leftJoin("songs", "quests.song_id", "=", "songs.id")
            ->leftJoin("clients", "quests.client_id", "=", "clients.id")
            ->leftJoin("statuses", "quests.status_id", "=", "statuses.id")
            ->orderBy("quests.created_at", "desc");
        $requests = Request::leftJoin("clients", "requests.client_id", "=", "clients.id")
            ->leftJoin("statuses", "requests.status_id", "=", "statuses.id")
            ->where("status_id", "!=", 9)
            ->orderBy("requests.created_at");
        if(Auth::id() != 1){
            $quests = $quests->where("client_id", $client->id);
            $requests = $requests->where("client_id", $client->id);
        }
        $quests = $quests->get(); //TODO dodać selecta
        $requests = $requests->get([
            "requests.id",
            "title",
            "artist",
            "requests.client_name as rq_client_name",
            "clients.client_name as cl_client_name",
            "status_id",
            "status_name",
        ]);

        return view("quests", [
            "title" => "Lista zleceń",
            "quests" => $quests,
            "requests" => $requests
        ]);
    }

    public function quest(){
        $quest = [];

        return view("quest", [
            "title" => "Zlecenie",
            "quest" => $quest
        ]);
    }
    public function request($id){
        $request = Request::leftJoin("clients", "requests.client_id", "=", "clients.id")
        ->leftJoin("statuses", "requests.status_id", "=", "statuses.id")
        ->where("requests.id", $id)
        ->get();
        // ->get([
        //     ""
        // ]);
        $prices = DB::table("prices")->pluck("service", "indicator")->toArray();

        return view("request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices")));
    }

    public function addRequest(){
        $questTypes = ["podkład muzyczny", "nuty", "występ"];
        $questTypes = array_combine($questTypes, $questTypes);

        $prices = DB::table("prices")->pluck("service", "indicator")->toArray();

        return view("add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices")));
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
            $request->client_name = $rq->client_name;
            $request->email = $rq->email;
            $request->phone = $rq->phone;
            $request->other_medium = $rq->other_medium;
            $request->contact_preference = $rq->contact_preference ?? "email";
            $request->hard_deadline = $rq->hard_deadline ?? false;
        }
        $request->title = $rq->title;
        $request->artist = $rq->artist;
        $request->cover_artist = $rq->cover_artist;
        $request->link = $rq->link;
        $request->price = $rq->price;
        $request->deadline = $rq->deadline;

        $request->wishes = ($request->wishes == null) ? $rq->wishes : $request->wishes."\n".$rq->wishes;


        $request->quest_type = $rq->quest_type;
        $request->made_by_me = true;
        $request->status_id = 1;
        $request->save();

        return redirect("quests")->with("success", "Dodano nowe zapytanie");
    }
}
