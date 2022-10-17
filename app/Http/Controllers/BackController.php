<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackController extends Controller
{
    public function dashboard(){
        $quests = Quest::join("songs", "quests.song_id", "=", "songs.id")
            ->join("clients", "quests.client_id", "=", "clients.id")
            ->join("statuses", "quests.status_id", "=", "statuses.id")
            ->whereRaw("status_id not in (7, 8, 18, 9, 19)")
            ->orderByRaw("case when deadline is null then 1 else 0 end, deadline");
        if(Auth::id() != 1){
            $client_id = Client::where("user_id", Auth::id())->value("id");
            $quests = $quests->where("client_id", $client_id);
        }
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
            "deadline"
        ]);

        return view("dashboard", [
            "title" => "Podsumowanie",
            "quests" => $quests,
            'extraCss' => 'back'
        ]);
    }
}
