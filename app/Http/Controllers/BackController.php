<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

        $requests = Request::join("clients", "requests.client_id", "=", "clients.id")
            ->join("statuses", "requests.status_id", "=", "statuses.id")
            ->whereNotIn("status_id", [7, 8, 9])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        $quests = Quest::join("songs", "quests.song_id", "=", "songs.id")
            ->join("clients", "quests.client_id", "=", "clients.id")
            ->join("statuses", "quests.status_id", "=", "statuses.id")
            ->whereNotIn("status_id", [18, 19])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
            $quests = $quests->where("client_id", $client->id);
        }
        $requests = $requests->get(
            "requests.id",
            "title",
            "artist",
            "coalesce(requests.client_name, clients.client_name) as client_name",
            "coalesce(requests.surname, clients.surname) as surname",
            "status_id",
            "status_name",
            "price",
            "deadline",
            "hard_deadline"
        );
        $quests = $quests->get([
            "quests.id",
            "title",
            "artist",
            "client_name",
            "surname",
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

        $quests = Quest::join("songs", "quests.song_id", "=", "songs.id")
            ->join("clients", "quests.client_id", "=", "clients.id")
            ->join("statuses", "quests.status_id", "=", "statuses.id")
            ->orderBy("quests.created_at", "desc");
        $requests = Request::join("clients", "requests.client_id", "=", "clients.id")
            ->join("statuses", "requests.status_id", "=", "statuses.id")
            ->where("status_id", "!=", 9)
            ->orderBy("requests.created_at");
        if(Auth::id() != 1){
            $quests = $quests->where("client_id", $client->id);
            $requests = $requests->where("client_id", $client->id);
        }
        $quests = $quests->get();
        $requests = $requests->get();

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
    public function request(){
        $request = [];

        return view("quest", [
            "title" => "Zapytanie",
            "quest" => $request
        ]);
    }
}