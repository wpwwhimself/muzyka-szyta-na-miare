<?php

namespace App\Http\Controllers;

use App\Models\Composition;
use App\Models\DjSet;
use App\Models\Genre;
use App\Models\ShowcasePlatform;
use Illuminate\Http\Request;

class DjController extends Controller
{
    public function index()
    {
        $djReadyCount = Composition::get()
            ->filter(fn ($c) => $c->is_dj_ready)
            ->count();
        $djSetsCount = DjSet::get()->count();

        return view("pages.".user_role().".dj.index", compact(
            "djReadyCount",
            "djSetsCount",
        ));
    }

    #region gig mode

    #endregion

    #region lottery mode
    public function lotteryMode()
    {
        return view("pages.".user_role().".dj.lottery-mode");
    }

    public function lotteryData(Request $rq)
    {
        $compositions = Composition::get()
            ->filter(fn ($c) => $c->is_dj_ready)
            ->values();
        $genres = [
            "blues",
            "funk",
            "kołysanka",
            "reggae",
            "rock&roll",
            "umpa-umpa",
        ];

        return response()->json([
            "data" => compact("compositions", "genres"),
            "compositionSummary" => "Dostępne: " . count($compositions),
            "genreSummary" => "Dostępne: " . count($genres),
            "compositionsList" => collect($compositions)->map(fn ($c, $i) =>
                "<span class='interactive' onclick='pickComposition($i)'>
                    <span class='accent primary'>$c[title]</span>
                    <small class='ghost'>$c[composer]</small>
                </span>"
            )->join(""),
        ]);
    }
    #endregion
}
