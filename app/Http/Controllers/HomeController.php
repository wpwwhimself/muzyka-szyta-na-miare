<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\Request;
use App\Models\QuestType;
use App\Models\Showcase;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $showcases = Showcase::orderBy("song_id", "desc")->orderBy("updated_at", "desc")->limit(3)->get();

        $prices = DB::table("prices")->where("operation", "+")->get(["service", "quest_type_id", "price_".strtolower(CURRENT_PRICING())." AS price"]);

        $quest_types_raw = QuestType::all()->toArray();
        foreach($quest_types_raw as $val){
            $quest_types[$val["id"]] = $val["type"];
        }

        $average_quest_done = 3; //TODO obliczyć średni czas wykonania questa
        $quests_completed = Quest::where("status_id", 19)->count();
        $quests_originals_completed = Quest::where("price_code_override", "like", "%d%")->where("status_id", 19)->count();

        $songs = Song::orderBy("artist")->get(); //TODO ZEBRAĆ I PRZEDSTAWIĆ PIOSENKI

        $contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne"
        ];

        return view("front", compact(
            "showcases",
            "prices",
            "songs",
            "quest_types",
            "contact_preferences",
            "average_quest_done",
            "quests_completed",
            "quests_originals_completed"
        ));
    }
}
