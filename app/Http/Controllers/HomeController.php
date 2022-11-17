<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestType;

class HomeController extends Controller
{
    public function index(){
        $quest_types_raw = QuestType::all()->toArray();
        foreach($quest_types_raw as $val){
            $quest_types[$val["id"]] = $val["type"];
        }
        return view("front", compact("quest_types"));
    }
}
