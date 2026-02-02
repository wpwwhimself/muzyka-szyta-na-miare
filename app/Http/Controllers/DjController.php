<?php

namespace App\Http\Controllers;

use App\Models\Composition;
use App\Models\DjSampleSet;
use App\Models\DjSet;
use App\Models\DjSong;
use App\Models\Genre;
use App\Models\ShowcasePlatform;
use Illuminate\Http\Request;

class DjController extends Controller
{
    public function index()
    {
        return view("pages.".user_role().".dj.index");
    }

    #region songs
    public function listSongs()
    {
        $songs = DjSong::orderBy("title")->paginate(25);

        return view("pages.".user_role().".dj.songs.list", compact(
            "songs",
        ));
    }

    public function editSong($id = null)
    {
        $song = DjSong::find($id);
        $tempos = collect(DjSong::TEMPOS)->mapWithKeys(fn ($t) => [$t["code"] => "$t[icon] $t[label]"])->toArray();
        $genres = Genre::ordered()->get()->pluck("name", "id");
        $potential_sample_sets = DjSampleSet::orderBy("name")->get()->mapWithKeys(fn ($s) => [$s->id => $s->full_name]);
        // $showcase = //todo odblokować jak już będą showcase'y
        $showcase_platforms = ShowcasePlatform::orderBy("ordering")->get()
            ->pluck("name", "code");

        $platform_suggestion = ShowcasePlatform::suggest()["code"];
        //todo odblokować, jak już będą showcase'y
        // if (!$showcase && $platform_suggestion) {
        //     $showcase_platforms[$platform_suggestion] .= " (sugerowana)";
        // }

        return view("pages.".user_role().".dj.songs.edit", compact(
            "song",
            "tempos",
            "genres",
            "potential_sample_sets",
            // "showcase",
            "showcase_platforms",
            "platform_suggestion",
        ));
    }

    public function processSong(Request $rq)
    {
        $data = $rq->except(["_token", "action"]);
        foreach (DjSong::PROCESSABLEJSONS as $key) {
            $data[$key] = DjSong::processJsonForEdit($data[$key]);
        }
        $data["has_project_file"] = $rq->has("has_project_file");

        if ($rq->get("action") == "save") {
            $song = DjSong::updateOrCreate(["id" => $data["id"]], $data);
            return redirect()->route("dj-edit-song", ["id" => $song->id])->with("toast", ["success", "Utwór poprawiony"]);
        } else if ($rq->get("action") == "delete") {
            DjSong::find($data["id"])->delete();
            return back()->with("toast", ["success", "Utwór usunięty"]);
        }

        abort(400, "Niewłaściwa akcja formularza");
    }
    #endregion

    #region sample sets
    public function listSampleSets()
    {
        $sets = DjSampleSet::orderBy("name")->paginate(25);

        return view("pages.".user_role().".dj.sample-sets.list", compact(
            "sets",
        ));
    }

    public function editSampleSet($id = null)
    {
        $set = DjSampleSet::find($id);

        return view("pages.".user_role().".dj.sample-sets.edit", compact(
            "set",
        ));
    }

    public function processSampleSet(Request $rq)
    {
        $data = $rq->except(["_token", "action", "songs"]);

        if ($rq->get("action") == "save") {
            $set = DjSampleSet::updateOrCreate(["id" => $data["id"]], $data);
            return redirect()->route("dj-edit-sample-set", ["id" => $set->id])->with("toast", ["success", "Sample poprawiony"]);
        } else if ($rq->get("action") == "delete") {
            DjSampleSet::find($data["id"])->delete();
            return back()->with("toast", ["success", "Utwór usunięty"]);
        }

        abort(400, "Niewłaściwa akcja formularza");
    }
    #endregion

    #region sets
    public function listSets()
    {
        $sets = DjSet::orderBy("name")->paginate(25);

        return view("pages.".user_role().".dj.sets.list", compact(
            "sets",
        ));
    }

    public function editSet($id = null)
    {
        $set = DjSet::find($id);
        $songs = DjSong::orderBy("title")->get()
            ->map(fn ($s) => "$s->id: $s->full_title")
            ->mapWithKeys(fn ($s) => [$s => $s])
            ->toArray();
        $sampleSets = DjSampleSet::orderBy("name")->get()->mapWithKeys(fn ($s) => [$s->id => $s->full_name]);

        return view("pages.".user_role().".dj.sets.edit", compact(
            "set",
            "songs",
            "sampleSets",
        ));
    }

    public function processSet(Request $rq)
    {
        $data = $rq->except(["_token", "action", "songs"]);

        if ($rq->get("action") == "save") {
            $set = DjSet::updateOrCreate(["id" => $data["id"]], $data);
            $set->songs()->sync($rq->get("songs"));
            return redirect()->route("dj-edit-set", ["id" => $set->id])->with("toast", ["success", "Utwór poprawiony"]);
        } else if ($rq->get("action") == "delete") {
            DjSet::find($data["id"])->delete();
            return back()->with("toast", ["success", "Utwór usunięty"]);
        }

        abort(400, "Niewłaściwa akcja formularza");
    }
    #endregion

    #region gig mode
    public function gigMode()
    {
        return view("pages.".user_role().".dj.gig-mode");
    }

    public function gigModeInit()
    {
        $songs = DjSong::orderBy("title")
            ->select(["id", "title", "artist", "tempo"])
            ->get();
        $sets = DjSet::withCount("songs")
            ->orderBy("name")
            ->get();
        $sampleSets = DjSampleSet::withCount("songs")
            ->orderBy("name")
            ->get();

        return response()->json(compact(
            "songs",
            "sets",
            "sampleSets",
        ));
    }

    public function gigModeSong($id)
    {
        $song = DjSong::find($id);

        return response()->json($song);
    }

    public function gigModeSet($id)
    {
        $set = DjSet::with("songs")->find($id);

        return response()->json($set);
    }

    public function gigModeSampleSet($id)
    {
        $set = DjSampleSet::with("songs")->find($id);

        return response()->json($set);
    }
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
