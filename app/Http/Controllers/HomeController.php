<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestType;
use App\Models\Showcase;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $showcases = Showcase::orderBy("quest_id", "desc")->orderBy("updated_at", "desc")->limit(3)->get();

        $prices = DB::table("prices")->where("operation", "+")->get();
        
        $quest_types_raw = QuestType::all()->toArray();
        foreach($quest_types_raw as $val){
            $quest_types[$val["id"]] = $val["type"];
        }

        $contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne"
        ];

        return view("front", compact("showcases", "prices", "quest_types", "contact_preferences"));
    }
}
