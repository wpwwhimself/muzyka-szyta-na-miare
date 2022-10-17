<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackController extends Controller
{
    public function dashboard(){
        if(Auth::id() == 1){
            $quests = Quest::join("songs", "quests.song_id", "=", "songs.id")
                ->join("clients", "quests.client_id", "=", "clients.id")
                ->join("statuses", "quests.status_id", "=", "statuses.id")
                ->orderByRaw("case when deadline is null then 1 else 0 end, deadline")
                ->get([
                    "quests.id",
                    "title",
                    "artist",
                    "client_name",
                    "surname",
                    "status_id",
                    "status_name",
                    "price",
                    "paid",
                    "deadline"
                ]);
        }else{
            $client_id = Client::where("user_id", Auth::id())->value("id");
            $quests = Quest::join("songs", "quests.song_id", "=", "songs.id")
                ->join("clients", "quests.client_id", "=", "clients.id")
                ->join("statuses", "quests.status_id", "=", "statuses.id")
                ->where("client_id", $client_id)
                ->orderByRaw("case when deadline is null then 1 else 0 end, deadline")
                ->get([
                    "quests.id",
                    "title",
                    "artist",
                    "client_name",
                    "surname",
                    "status_id",
                    "status_name",
                    "price",
                    "paid",
                    "deadline"
                ]);
        }

        return view("dashboard", [
            "title" => "Podsumowanie",
            "quests" => $quests,
            'extraCss' => 'back'
        ]);
    }
}
