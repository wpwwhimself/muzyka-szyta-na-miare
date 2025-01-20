<?php

namespace App\Http\Controllers;

use App\Models\ClientShowcase;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowcaseController extends Controller
{
    public function list(){
        $showcases = Showcase::orderBy("updated_at", "desc")->paginate(5);
        $client_showcases = ClientShowcase::orderBy("updated_at", "desc")->paginate(5);

        $songs_raw = Song::whereDoesntHave('showcase')
            ->whereHas('quests', function($q){
                $q->where('status_id', 19);
            })
            ->orderByDesc("created_at")
            ;

        $potential_showcases = clone $songs_raw;
        $potential_showcases = $potential_showcases->whereDate('created_at', '>', Carbon::today()->subMonth()->format("Y-m-d H:i:s"))->get();

        $songs_raw = $songs_raw->get()->toArray();
        foreach($songs_raw as $song){
            $songs[$song["id"]] = "$song[title] ($song[artist]) [$song[id]]";
        }

        $all_songs = Song::orderBy("title")
            ->orderBy("artist")
            ->orderBy("id")
            ->get()
            ->mapWithKeys(fn($s) => [$s["id"] => "$s[title] ($s[artist]) [$s[id]]"]);

        return view(user_role().".showcases", array_merge(
            ["title" => "Lista reklam"],
            compact("showcases", "client_showcases", "songs", "all_songs", "potential_showcases")
        ));
    }

    public function add(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        Showcase::create([
            "song_id" => $rq->song_id,
            "link_fb" => (filter_var($rq->link_fb, FILTER_VALIDATE_URL)) ?
                "<a target='_blank' href='$rq->link_fb'>$rq->link_fb</a>" : $rq->link_fb,
            "link_ig" => $rq->link_ig,
        ]);

        return back()->with("success", "Dodano pozycję");
    }

    public function addFromClient(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        ClientShowcase::create([
            "song_id" => $rq->song_id,
            "embed" => $rq->embed,
        ]);

        return back()->with("success", "Dodano pozycję");
    }

    public function pinComment(int $id)
    {
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());

        $entry = StatusChange::find($id);
        $entry->update(["pinned" => !$entry->pinned]);

        return back()->with("success", "Zmieniono przypięcie");
    }
}
