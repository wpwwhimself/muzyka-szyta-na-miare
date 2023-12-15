<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatchController extends Controller
{
    public function patchQuest($id, $mode = "key-value", Request $rq){
        $data = Quest::findOrFail($id);
        if($mode == "single"){
            $data->{$rq->key} = $rq->value;
        }elseif($mode == "key-value"){
            foreach($rq->all() as $key => $value){
                $data->{Str::snake($key)} = $value;
            }
        }
        $data->save();
        return response()->json(["patched" => $rq->all(), "quest" => $data]);
    }

    public function patchSong($id, $mode = "key-value", Request $rq){
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
}
