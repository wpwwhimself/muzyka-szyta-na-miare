<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quest;
use App\Models\StatusChange;

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
        ];
        $clients_counts = [
            "weteranów" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, ">=", VETERAN_FROM())->count(),
            "biegłych" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [4, VETERAN_FROM()-1]);
            })->count(),
            "zainteresowanych" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [2, 4-1]);
            })->count(),
            "nowicjuszy" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 1)->count(),
            "debiutantów" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 0)->count(),
        ];

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact(
                "big_summary",
                "clients_summary", "clients_counts"
            ),
        ));
    }
}
