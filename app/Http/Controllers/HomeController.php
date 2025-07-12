<?php

namespace App\Http\Controllers;

use App\Models\ClientShowcase;
use App\Models\DjShowcase;
use App\Models\Genre;
use App\Models\OrganShowcase;
use App\Models\QuestType;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\SongTag;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view("front.index");
    }

    public function podklady()
    {
        $showcases = Showcase::orderBy("created_at", "desc")->limit(5)->get();
        $client_showcases = ClientShowcase::orderBy("updated_at", "desc")->limit(3)->get();
        $pinned_comments = StatusChange::where("pinned", true)->orderBy("date", "desc")->get();

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

        $random_song = Song::all()->random();

        $contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne"
        ];

        $genres = Genre::orderBy("name")->get();
        $song_tags = SongTag::orderBy("name")->get();

        return view("front.podklady", compact(
            "showcases",
            "client_showcases",
            "pinned_comments",
            "prices",
            "quest_types",
            "contact_preferences",
            "random_song",
            "average_quest_done",
            "genres",
            "song_tags",
        ));
    }

    public function organista()
    {
        $showcases = OrganShowcase::orderBy("created_at", "desc")->limit(5)->get();

        return view("front.organista", compact(
            "showcases",
        ));
    }

    public function dj()
    {
        $showcases = DjShowcase::orderBy("created_at", "desc")->limit(5)->get();
        $genres = Genre::orderBy("name")->get();

        return view("front.dj", compact(
            "showcases",
            "genres",
        ));
    }
}
