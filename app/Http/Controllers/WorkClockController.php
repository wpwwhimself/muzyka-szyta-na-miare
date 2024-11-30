<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkClockController extends Controller
{
    public function main() {
        return view(user_role().".studio", array_merge([
            "title" => "Studio",
        ]));
    }

    public function activeQuests(Request $rq) {
        $data = Quest::with("song", "client", "status")
            ->whereIn("status_id", [12])
            ->orderBy("deadline")
            ->get();

        return response()->json($data);
    }

    public function index($quest_id) {
        $quest = Quest::find($quest_id);

        return view(user_role().".studio-view", array_merge([
            "title" => implode(" | ", [
                $quest->song->title ?? "Utwór bez tytułu",
                "Studio"
            ]),
        ], compact("quest")));
    }

    public function modes() {
        $data = Status::where("id", ">=", 100)
            ->orderBy("status_name")
            ->get()
        ;

        return response()->json($data);
    }

    public function songDataByQuest($quest_id) {
        $data = Quest::with("song.workTime")->find($quest_id);

        return response()->json($data);
    }

    public function startStop(Request $rq){
        if(Auth::id() === 0) return response()->json(OBSERVER_ERROR(), 403);
        $now_working = SongWorkTime::where("now_working", 1)->first();
        $response = [];

        if($now_working){
            $now_working->update([
                "now_working" => 0,
                "time_spent" => Carbon::createFromTimeString($now_working->time_spent)
                        ->addSeconds(Carbon::createFromTimeString($now_working->since)->diffInSeconds(now()))
                        ->format("G:i:s")
            ]);
            $response["stopped"] = [
                "status_id" => $now_working->status_id,
                "time" => $now_working->time_spent,
            ];
        }

        if($rq->status_id != 13){
            $current = SongWorkTime::updateOrCreate([
                "status_id" => $rq->status_id,
                "song_id" => $rq->song_id,
            ],
            [
                "now_working" => 1,
                "since" => now(),
            ]);
            $response["started"] = [
                "status_id" => $rq->status_id,
                "time" => $current->time_spent,
            ];
        }

        $response["now_working"] = $rq->status_id != 13;

        return response()->json($response);
    }

    public function remove($song_id, $status_id){
        if(Auth::id() === 0) return response()->json(OBSERVER_ERROR(), 403);
        SongWorkTime::where("song_id", $song_id)->where("status_id", $status_id)->first()->delete();
        $response = implode(" ", [
            "Deleted recording for",
            $status_id,
        ]);
        return response()->json($response);
    }
}
