<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Showcase;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $showcases = Showcase::orderBy("updated_at", "desc")->limit(3)->get();

        $prices = DB::table("prices")->where("operation", "+")->get(["service", "quest_type_id", "price_".strtolower(CURRENT_PRICING())." AS price"]);

        $quest_types_raw = QuestType::all()->toArray();
        foreach($quest_types_raw as $val){
            $quest_types[$val["id"]] = $val["type"];
        }

        $diffs = [];
        foreach(StatusChange::whereIn("new_status_id", [11, 15])->orderBy("date")->get() as $stts){
            $diffs[$stts->re_quest_id] =
                (isset($diffs[$stts->re_quest_id])) ?
                (
                    (is_numeric($diffs[$stts->re_quest_id])) ?
                    $diffs[$stts->re_quest_id] : //jeśli już jest policzone, to zostaw
                    $diffs[$stts->re_quest_id]->diffInDays(Carbon::parse($stts->date)) + 1
                ) :
                Carbon::parse($stts->date);
        }
        $diffs = array_filter($diffs, function($val){ return is_numeric($val); });
        $average_quest_done = (count($diffs) == 0) ? 0 : round(array_sum($diffs)/count($diffs));

        $quests_completed = Quest::where("status_id", 19)->count();
        $quests_originals_completed = Quest::where("price_code_override", "like", "%d%")->where("status_id", 19)->count();

        $contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne"
        ];

        return view("front", compact(
            "showcases",
            "prices",
            "quest_types",
            "contact_preferences",
            "average_quest_done",
            "quests_completed",
            "quests_originals_completed"
        ));
    }
}
