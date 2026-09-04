<?php

namespace App\Http\Controllers;

use andcarpi\Popper\Facades\Popper;
use App\Mail\ArchmageQuestMod;
use App\Mail\PatronRejected;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\User;
use App\Scaffolds\Modal;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackController extends Controller
{
    #region prices
    public function prices(){
        $prices = DB::table("prices")->get();

        $discount = (is_archmage()) ? null : (
            (Auth::user()->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
            +
            (Auth::user()->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
        );

        $clients = [];
        if (is_archmage()) {
            $clients_raw = User::clients()->get();
            foreach($clients_raw as $client){
                $clients[] = ["value" => $client->id, "label" => _ct_($client->display_name ." «" . $client->id . "»")];
            }
        }

        $quest_types = QuestType::all()->pluck("type", "id")->toArray();
        $minimal_prices = array_combine($quest_types, QUEST_MINIMAL_PRICES());

        return view("pages.".user_role().".prices", array_merge(
            ["title" => "Cennik"],
            compact("prices", "discount", "minimal_prices", "clients")
        ));
    }
    #endregion

    public static function newStatusLog($re_quest_id, $new_status_id, $comment, $changed_by = null, $mailing = null, $changes = null){
        if($re_quest_id){
            $client_id = is_request($re_quest_id) ?
                Request::find($re_quest_id)->client_id :
                Quest::find($re_quest_id)->client_id;
        }else{
            $client_id = $changed_by;
        }

        StatusChange::insert([
            "re_quest_id" => $re_quest_id,
            "new_status_id" => $new_status_id,
            "changed_by" => ($client_id == null && in_array($new_status_id, [1, 6, 8, 9, 96])) ? null : $changed_by ?? Auth::id(),
            "comment" => $comment,
            "values" => $changes ? json_encode($changes) : null,
            "mail_sent" => $mailing,
            "date" => now(),
        ]);
    }

    public function setPatronLevel($client_id, $level){
        if(Auth::id() === 0) return redirect()->route("profile")->with("toast", ["error", OBSERVER_ERROR()]);
        $client = User::findOrFail($client_id);

        $client->update(["helped_showcasing" => $level]);
        $mailing = false;
        if($level == 0 && $client->can_be_mailed){
            Mail::to($client->email)->send(new PatronRejected($client->fresh()));
            $mailing = true;
        }

        if(Auth::id() == 1) return redirect()->route("profile")->with("toast", ["success", (($level == 2) ? "Wniosek przyjęty" : "Wniosek odrzucony").($mailing ? ", mail wysłany" : "")]);
        return redirect()->route("profile")->with("toast", ["success", "Wystawienie opinii odnotowane"]);
    }

    public function ppp($page = "0-index"){
        $titles = [];
        foreach(File::allFiles(resource_path("views/doc")) as $key => $ttl){
            $titles[$key] = preg_replace('/(.*)doc[\/\\\](.*)\.blade\.php/', "$2", $ttl);
        }

        return view("pages.".user_role().".ppp", array_merge(
            ["title" => "Poradnik Przyszłych Pokoleń"],
            compact("page", "titles")
        ));
    }

    #region re_quests
    public function restatusReQuestWithComment(HttpRequest $rq)
    {
        $scope = Str::plural($rq->get("model"));
        $model = model($scope)::find($rq->get("id"));

        $model->update([
            "status_id" => $rq->get("newStatus"),
        ]);
        $flash_content = "Status ".($scope == "requests" ? "zapytania" : "zlecenia")." zmieniony";

        self::newStatusLog(
            $model->id,
            $rq->get("newStatus"),
            $rq->get("comment"),
            $rq->get("changedBy")
        );

        // mail
        Mail::to(env("MAIL_MAIN_ADDRESS"))->send(new ArchmageQuestMod($model->fresh()));
        $mailing = true;
        $flash_content .= ", mail wysłany";
        if($mailing !== null) $model->history->first()->update(["mail_sent" => $mailing]);

        return redirect()->route($rq->get("model"), ["id" => $model->id])->with("toast", ["success", $flash_content]);
    }
    #endregion

    #region lookups
    public function lookupUsers()
    {
        $fieldName = "client_id";
        $data = User::clients()
            ->get()
            ->map(fn ($u) => collect([
                "id" => $u->id,
                "name" => $u->display_name,
                "email" => $u->can_be_mailed ? $u->email : null,
                "phone" => $u->phone,
            ]))
            ->filter(fn ($u) =>
                Str::contains($u["id"], request("query"), true)
                || Str::contains($u["name"], request("query"), true)
                || Str::contains($u["email"], request("query"), true)
                || Str::contains($u["phone"], request("query"))
            )
            ->values();
        $headings = collect([
            "ID",
            "Nazwisko",
            "Email",
            "Telefon",
        ]);

        return view("shipyard::components.ui.lookup-results", compact(
            "data",
            "headings",
            "fieldName",
        ))->render();
    }

    public function lookupSongs()
    {
        $fieldName = "song_id";
        $data = Song::where(fn ($q) => $q
            ->where("id", "regexp", request("query"))
            ->orWhere("title", "regexp", request("query"))
            ->orWhere("artist", "regexp", request("query"))
            ->orWhere("link", "regexp", request("query"))
        )
            ->get()
            ->map(fn ($s) => collect([
                "id" => $s->id,
                "title" => $s->title,
                "artist" => $s->artist,
                "link" => view("components.link-interpreter", ['raw' => $s->link])->render(),
                "composition" => $s->composition?->full_title,
            ]))
            ->values();
        $headings = collect([
            "ID",
            "Tytuł",
            "Wykonawca",
            "Linki",
            "Kompozycja",
        ]);

        return view("shipyard::components.ui.lookup-results", compact(
            "data",
            "headings",
            "fieldName",
        ))->render();
    }
    #endregion
}
