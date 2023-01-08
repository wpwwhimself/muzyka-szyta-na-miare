<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function dashboard(){
        $yes = null;

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact("yes"),
        ));
    }
}
