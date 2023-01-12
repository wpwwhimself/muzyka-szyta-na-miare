<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\StatusChange;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function dashboard(){
        $big_summary = [
            "Biznes kręci się od" => date_diff(date_create('2020-01-01'), date_create())->format("%y lat, %m miesięcy i %d dni"),
            "Liczba skończonych questów" => Quest::where("status_id", 19)->count(),
            "Zarobiono w sumie" => number_format(StatusChange::where("new_status_id", 32)->sum("comment"), 2, ",", " ")." zł",
        ];

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact("big_summary"),
        ));
    }
}
