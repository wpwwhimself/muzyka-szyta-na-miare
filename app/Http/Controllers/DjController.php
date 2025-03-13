<?php

namespace App\Http\Controllers;

use App\Models\DjSong;
use Illuminate\Http\Request;

class DjController extends Controller
{
    public function index()
    {
        return view("dj.index");
    }

    #region songs
    public function listSongs()
    {
        $songs = DjSong::orderBy("title")->paginate(25);

        return view("dj.songs.list", compact(
            "songs",
        ));
    }

    public function editSong($id = null)
    {
        $song = DjSong::find($id);
        $tempos = collect(DjSong::TEMPOS)->mapWithKeys(fn ($t) => [$t["code"] => "$t[icon] $t[label]"])->toArray();

        return view("dj.songs.edit", compact(
            "song",
            "tempos",
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
            return redirect()->route("dj-edit-song", ["id" => $song->id])->with("success", "Utwór poprawiony");
        } else if ($rq->get("action") == "delete") {
            DjSong::find($data["id"])->delete();
            return back()->with("success", "Utwór usunięty");
        }

        abort(400, "Niewłaściwa akcja formularza");
    }
    #endregion

    #region gig mode
    public function gigMode()
    {
        return view("dj.gig-mode");
    }

    public function gigModeInit()
    {
        $songs = DjSong::orderBy("title")
            ->select(["id", "title", "artist", "tempo"])
            ->get();

        return response()->json(compact(
            "songs",
        ));
    }

    public function gigModeSong($id)
    {
        $song = DjSong::find($id);

        return response()->json($song);
    }

    public function gigModeSet()
    {

    }
    #endregion
}
