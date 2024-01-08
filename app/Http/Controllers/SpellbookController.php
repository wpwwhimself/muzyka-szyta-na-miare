<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request as ModelsRequest;
use App\Models\StatusChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpellbookController extends Controller
{
    public static $MISSPELL_ERROR = "Zaklęcie tylko dla zaawansowanych";

    public function obliterate($id){
        StatusChange::where("re_quest_id", $id)->delete();
        ModelsRequest::find($id)->delete();
        return redirect()->route("dashboard")->with("success", "Zapytanie wymazane");
    }

    public function silence($id){
        StatusChange::where("re_quest_id", $id)->orderByDesc("date")->first()->delete();
        return back()->with("success", "Ostatni status uciszony");
    }

    public function transmute($id, $property, $value = null){
        $r = (strlen($id) == 36) ? ModelsRequest::find($id) : Quest::find($id);
        $r->{$property} = $value;
        $r->save();
        return back()->with("success", "Atrybut zmieniony");
    }

    public function restatus($id, $status_id){
        Quest::find($id)->update(["status_id" => $status_id]);
        StatusChange::where("re_quest_id", $id)
            ->whereNotIn("new_status_id", [32])
            ->orderByDesc("date")
            ->first()
            ->update(["new_status_id" => $status_id]);
        return back()->with("success", "Faza zmieniona siłą");
    }

    public function polymorph($id, $letter){
        if(!in_array($letter, QuestType::all()->pluck("code")->toArray()))
            return back()->with("error", "Niewłaściwa litera");

        $new_quest_id = next_quest_id($letter);
        $new_song_id = next_song_id($letter);
        $quest = Quest::find($id);
        $old_song_id = $quest->song_id;

        $quest->update(["id" => $new_quest_id]);
        $quest->song->update(["id" => $new_song_id]);
        StatusChange::where("re_quest_id", $id)->update(["re_quest_id" => $new_quest_id]);
        if(Storage::exists("safe/$old_song_id")){
            Storage::rename("safe/$old_song_id", "safe/$new_song_id");
        }
        if(Storage::exists("showcases/$old_song_id.ogg")){
            Storage::rename("showcases/$old_song_id.ogg", "showcases/$new_song_id.ogg");
        }

        return redirect()->route("quest", ["id" => $new_quest_id])->with("success", "Zlecenie przemianowane");
    }
}
