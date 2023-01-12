<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use App\Models\StatusChange;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function dashboard(){
        $big_summary = [
            "biznes kręci się od" => date_diff(date_create('2020-01-01'), date_create())->format("%y lat, %m miesięcy i %d dni"),
            "liczba skończonych questów" => Quest::where("status_id", 19)->count(),
            "zarobiono w sumie" => number_format(StatusChange::where("new_status_id", 32)->sum("comment"), 2, ",", " ")." zł",
        ];
        $clients_summary = [
            "łącznie" => Client::count(),
            "zaufanych" => Client::where("trust", 1)->count(),
            "krętaczy" => Client::where("trust", -1)->count(),
            "patronów" => Client::where("helped_showcasing", 2)->count(),
        ];
        $clients_counts = [
            "weterani (".VETERAN_FROM()."+)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, ">=", VETERAN_FROM())->count(),
            "biegli (4-".(VETERAN_FROM()-1).")" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [4, VETERAN_FROM()-1]);
            })->count(),
            "zainteresowani (2-3)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [2, 4-1]);
            })->count(),
            "nowicjusze (1)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 1)->count(),
            "debiutanci (0)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 0)->count(),
        ];
        $new_clients = array_count_values(Client::whereDate("created_at", ">=", Carbon::now()->subYear())->get("created_at")->map(function($date){
            return($date->created_at->format("Y-m"));
        })->toArray());

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact(
                "big_summary",
                "clients_summary", "clients_counts", "new_clients"
            ),
        ));
    }
}
