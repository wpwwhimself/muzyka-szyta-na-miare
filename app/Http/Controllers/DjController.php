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
        $djSetsCount = DjSet::ready()->count();

        return view("pages.".user_role().".dj.index", compact(
            "djReadyCount",
            "djSetsCount",
        ));
    }

    public function processAddSet(Request $rq)
    {
        $set = DjSet::create([
            "id" => DjSet::nextSetId($rq->tempo),
            "name" => $rq->name,
            "genre_id" => $rq->genre_id,
        ]);

        return redirect()->route("admin.model.edit", ["model" => "dj-sets", "id" => $set->id]);
    }

    #region gig mode
    public function gigMode()
    {
        return view("pages.".user_role().".dj.gig-mode");
    }

    public function gigData(Request $rq)
    {
        $sets = DjSet::ready()
            ->with("compositions")
            ->get()
            ->values();

        return response()->json([
            "data" => compact("sets"),
            "setSummary" => "Dostępne: " . count($sets),
            "setsList" => collect($sets)->map(fn ($s, $i) =>
                "<span class='interactive highlight' onclick='pickSet($i)'>
                    $s[id] $s[name]
                </span>"
            )->join(""),
        ]);
    }
    #endregion

    #region lottery mode
    public function lotteryMode()
    {
        return view("pages.".user_role().".dj.lottery-mode");
    }

    public function lotteryData(Request $rq)
    {
        $compositions = Composition::getDjReady()
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
