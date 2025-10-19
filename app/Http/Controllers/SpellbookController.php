<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request as ModelsRequest;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SpellbookController extends Controller
{
    public static $MISSPELL_ERROR = "Zaklęcie tylko dla zaawansowanych";

    public const SPELLS = [
        "become" => [
            "become/{user}",
        ],
        "obliterate" => [
            "requests/view/{id}/obliterate",
        ],
        "polymorph" => [
            "quests/view/{id}/polymorph/{letter}",
        ],
        "reprice" => [
            "requests/view/{id}/reprice/{new_code}",
            "quests/view/{id}/reprice/{new_code}",
        ],
        "restatus" => [
            "quests/view/{id}/restatus/{status_id}",
        ],
        "silence" => [
            "requests/view/{id}/silence",
            "quests/view/{id}/silence",
        ],
        "transmute" => [
            "requests/view/{id}/transmute/{property}/{value?}",
            "quests/view/{id}/transmute/{property}/{value?}",
        ],
    ];

    public function become(User $user)
    {
        Auth::login($user);
        return back()->with("success", "Jesteś teraz: $user->client_name");
    }

    public function obliterate($id){
        StatusChange::where("re_quest_id", $id)->delete();
        ModelsRequest::find($id)->delete();
        return redirect()->route("dashboard")->with("success", "Zapytanie wymazane");
    }

    public function silence($id){
        StatusChange::where("re_quest_id", $id)->orderByDesc("date")->first()->delete();
        $route_back = is_request($id) ? "request" : "quest";
        return redirect()->route($route_back, ["id" => $id])->with("success", "Ostatni status uciszony");
    }

    public function transmute($id, $property, $value = null){
        $r = is_request($id) ? ModelsRequest::find($id) : Quest::find($id);
        $r->{$property} = $value;
        $r->save();
        $route_back = is_request($id) ? "request" : "quest";
        return redirect()->route($route_back, ["id" => $id])->with("success", "Atrybut zmieniony");
    }

    public function restatus($id, $status_id){
        Quest::find($id)->update(["status_id" => $status_id]);
        StatusChange::where("re_quest_id", $id)
            ->whereNotIn("new_status_id", [32])
            ->orderByDesc("date")
            ->first()
            ->update(["new_status_id" => $status_id]);
        return redirect()->route("quest", ["id" => $id])->with("success", "Faza zmieniona siłą");
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

    public function reprice($id, $new_code)
    {
        $r = is_request($id) ? ModelsRequest::find($id) : Quest::find($id);
        $new_price = StatsController::runPriceCalc($new_code, $r->client_id);

        $r->update([
            (is_request($id) ? "price_code" : "price_code_override") => $new_price["labels"],
            "price" => $new_price["price"],
        ]);

        return back()->with("success", "Cena zmieniona");
    }
}
