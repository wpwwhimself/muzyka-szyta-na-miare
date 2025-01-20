<?php

namespace App\Http\Controllers;

use App\Models\FileTag;
use App\Models\Genre;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\SongTag;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SongController extends Controller
{
    public function list(Request $rq){
        $search = strtolower($rq->search ?? "");
        $songs = Song::orderBy("title")
            ->whereRaw("LOWER(title) like '%$search%'")
            ->orWhereRaw("LOWER(artist) like '%$search%'")
            ->orWhereRaw("LOWER(id) like '%$search%'")
            ->paginate();

        $song_work_times = [];
        $price_codes = [];
        foreach($songs as $song){
            $price_codes[$song->id] = [];
            for($i = 0; $i < strlen($song->price_code); $i++){
                $letter = $song->price_code[$i];
                if(preg_match('/[a-z]/', $song->price_code[$i])){
                    $price_codes[$song->id][] = DB::table("prices")->where("indicator", $letter)->value("service");
                }
            }
            $price_codes[$song->id] = implode("<br>", $price_codes[$song->id]);

            $song_work_times[$song->id] = [
                "total" => gmdate("H:i:s", DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->sum(DB::raw("TIME_TO_SEC(time_spent)"))),
                "parts" => DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->get()
                    ->toArray()
            ];
            $song_work_times[$song->id]["parts"] = implode("<br>", array_map(function($x){
                return Status::find($x->status_id)->status_name . " → " . $x->time_spent;
            }, $song_work_times[$song->id]["parts"]));
        }

        $tags = FileTag::orderBy("name")->get();

        return view(user_role().".songs.list", array_merge(
            ["title" => "Lista utworów"],
            compact("songs", "song_work_times", "price_codes", "search", "tags")
        ));
    }

    public function edit(string $id): View
    {
        $song = Song::findOrFail($id);
        $genres = Genre::orderBy("name")->get()->pluck("name", "id");
        $tags = SongTag::orderBy("name")->get();
        $showcase = Showcase::where("song_id", $song->id)->first();

        return view(user_role().".songs.edit", array_merge(
            ["title" => ($song->title ?? "Bez tytułu") . " | Edycja utworu"],
            compact("song", "genres", "tags", "showcase"),
        ));
    }

    public function process(Request $rq): RedirectResponse
    {
        $song = Song::findOrFail($rq->id);
        $song->update($rq->except("_token"));
        $song->tags()->sync(array_keys($rq->tags ?? []));

        if (array_filter($rq->only(["reel_platform", "reel_link"]))) {
            Showcase::updateOrCreate(
                ["song_id" => $song->id],
                [
                    "platform" => $rq->reel_platform,
                    "link" => $rq->reel_link,
                ],
            );
        } else {
            Showcase::where("song_id", $song->id)->delete();
        }

        return back()->with("success", "Utwór poprawiony");
    }

    #region genres
    public function listGenres(): View
    {
        $genres = Genre::orderBy("name")->get();
        return view(user_role().".songs.genres.list", array_merge(
            ["title" => "Gatunki utworów"],
            compact("genres"),
        ));
    }

    public function editGenre(string $id = null): View
    {
        $genre = Genre::find($id);
        return view(user_role().".songs.genres.edit", array_merge(
            ["title" => $genre ? $genre->name." | Edycja gatunku" : "Tworzenie gatunku"],
            compact("genre"),
        ));
    }

    public function processGenre(Request $rq): RedirectResponse
    {
        if ($rq->action == "save") {
            Genre::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
        } else if ($rq->action == "delete") {
            Genre::find($rq->id)->delete();
        }
        return redirect()->route("song-genres")->with("success", "Gatunek poprawiony");
    }

    #endregion

    #region tags
    public function listTags(): View
    {
        $tags = SongTag::orderBy("name")->get();
        return view(user_role().".songs.tags.list", array_merge(
            ["title" => "Tagi utworów"],
            compact("tags"),
        ));
    }

    public function editTag(string $id = null): View
    {
        $tag = SongTag::find($id);
        return view(user_role().".songs.tags.edit", array_merge(
            ["title" => $tag ? $tag->name." | Edycja tagu" : "Tworzenie taga"],
            compact("tag"),
        ));
    }

    public function processTag(Request $rq): RedirectResponse
    {
        if ($rq->action == "save") {
            SongTag::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
        } else if ($rq->action == "delete") {
            SongTag::find($rq->id)->delete();
        }
        return redirect()->route("song-tags")->with("success", "Tag poprawiony");
    }
    #endregion

    /////////////////////////////////////////

    public function patch($id, $mode = "key-value", Request $rq){
        $data = Song::findOrFail($id);
        if($mode == "single"){
            $data->{$rq->key} = $rq->value;
        }elseif($mode == "key-value"){
            foreach($rq->all() as $key => $value){
                $data->{Str::snake($key)} = $value;
            }
        }
        $data->save();
        return response()->json(["patched" => $rq->all(), "song" => $data]);
    }

    /////////////////////////////////////////

    public function getById(string $id){
        return Song::find($id)->toJson();
    }

    public function getForFront(){
        $songs = Song::with("tags")
            ->orderByRaw("ISNULL(title)")
            ->where("id", "not like", "O%")
            ->orderBy("title")
            ->orderBy("artist")
            ->get();

        return $songs;
    }

    public function changeLink(Request $rq){
        if(Auth::id() != 1) return;
        $id = $rq->id;
        Song::find($id)->update(["link" => $rq->link]);
    }

    public function getTags()
    {
        $tags = SongTag::orderBy("name")->get();
        return response()->json($tags ?? []);
    }
}
