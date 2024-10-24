<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Song;
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

        return view(user_role().".songs.list", array_merge(
            ["title" => "Lista utworów"],
            compact("songs", "song_work_times", "price_codes", "search")
        ));
    }

    public function edit(string $id): View
    {
        $song = Song::findOrFail($id);
        $genres = Genre::orderBy("name")->get()->pluck("name");
        return view(user_role().".songs.edit", array_merge(
            ["title" => $song->title . " | Edycja utworu"],
            compact("song", "genres"),
        ));
    }

    public function process(Request $rq): RedirectResponse
    {
        Song::findOrFail($rq->id)
            ->update($rq->except("_token"));
        return redirect()->route("songs")->with("success", "Utwór poprawiony");
    }

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
        $songs = Song::orderByRaw("ISNULL(title)")
            ->where("id", "not like", "O%")
            ->orderBy("title")
            ->orderBy("artist")
            ->select(["id", "title", "artist"])
            ->distinct()
            ->get();
    
        return $songs;
    }

    public function changeLink(Request $rq){
        if(Auth::id() != 1) return;
        $id = $rq->id;
        Song::find($id)->update(["link" => $rq->link]);
    }
}
