<?php

namespace App\Http\Controllers;

use andcarpi\Popper\Facades\Popper;
use App\Mail\ArchmageQuestMod;
use App\Mail\PatronRejected;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Shipyard\Modal;
use App\Models\Song;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\User;
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
    public function dashboard(){
        $user = Auth::user();

        $requests = Request::whereNotIn("status_id", [4, 7, 8, 9])
            ->orderBy("updated_at");
        $quests_ongoing = Quest::whereIn("status_id", STATUSES_WAITING_FOR_ME())
            ->orderByRaw("case status_id when 13 then 1 else 0 end")
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderByRaw("case status_id
                when 12 then 1
                when 11 or 14 or 16 or 21 or 26 or 96 then 5
                else 99
            end")
            ->orderByRaw("case when deadline <= now() + interval 1 day then 0 else 1 end")
            ->orderByRaw("case when hard_deadline is not null and hard_deadline < deadline then hard_deadline else deadline end")
            ->orderByRaw("case when price_code_override regexp 'z' and status_id in (11, 12, 16, 26, 96) then 0 else 1 end")
            ->orderByRaw("paid desc")
            ->orderBy("created_at");
        $quests_review = Quest::whereNotIn("status_id", [17, 18, 19])
            ->whereNotIn("status_id", STATUSES_WAITING_FOR_ME())
            ->orderByDesc("deadline")
            ->orderBy("created_at");

        if($user->hasRole("client", true)){
            $requests = $requests->where("client_id", $user->id);
            $quests_ongoing = $quests_ongoing->where("client_id", $user->id);
            $quests_review = $quests_review->where("client_id", $user->id);

            $quests_total = $user->notes->exp;
            $unpaids = Quest::where("client_id", Auth::id())
                ->whereNotIn("status_id", [18])
                ->where("paid", 0)
                ->get();
        }else{
            $recent = StatusChange::whereNotIn("new_status_id", [9, 32, 34])
                ->orderByDesc("date")
                ->limit(10)
                ->get();
            foreach($recent as $change){
                $change->is_request = is_request($change->re_quest_id);
                $change->re_quest = ($change->is_request) ?
                    Request::find($change->re_quest_id) :
                    Quest::find($change->re_quest_id);
                $change->new_status = Status::find($change->new_status_id);
            }
            $patrons_adepts = User::whereHas("notes", fn ($q) => $q->where("helped_showcasing", 1))->get();
            $showcases_missing = Quest::where("status_id", 19)
                ->whereDate("updated_at", ">", Carbon::today()->subWeeks(2))
                ->get()
                ->filter(fn($q) => !$q->song->has_showcase_file && $q->quest_type?->code == "P");

            $janitor_log = json_decode(Storage::get("janitor_log.json")) ?? [];
            foreach($janitor_log as $i){
                // translating subjects
                $length = strlen($i->subject);
                $replacement =
                    ($length == 36) ? Request::find($i->subject)
                    : (($length == 6) ? Quest::find($i->subject)
                    : Song::find($i->subject));
                $i->subject = $replacement ?? $i->subject;

                // translating operations
                if(in_array($i->comment, array_keys(JanitorController::$OPERATIONS))){
                    [$status_id, $comment_code] = explode("_", $i->comment);
                    $i->comment = [
                        "status_id" => $status_id,
                        "comment" => JanitorController::$OPERATIONS[$i->comment],
                    ];
                }
            }
        }
        $quests_ongoing = $quests_ongoing->get();
        $quests_review = $quests_review->get();
        $requests = $requests->get();

        return view(
            "pages.".user_role().".dashboard",
            !is_archmage()
                ? compact(
                    "quests_ongoing", "quests_review", "requests",
                    "quests_total",
                    "unpaids",
                )
                : compact(
                    "quests_ongoing", "quests_review", "requests",
                    "recent",
                    "patrons_adepts",
                    "showcases_missing",
                    "janitor_log",
                )
        );
    }

    #region prices
    public function prices(){
        $prices = DB::table("prices")->get();

        $discount = (is_archmage()) ? null : (
            (Auth::user()->notes->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
            +
            (Auth::user()->notes->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
        );

        $clients = [];
        if (is_archmage()) {
            $clients_raw = User::has("notes")->get();
            foreach($clients_raw as $client){
                $clients[] = ["value" => $client->id, "label" => _ct_($client->notes->client_name ." «" . $client->id . "»")];
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
        if(Auth::id() === 0) return redirect()->route("dashboard")->with("toast", ["error", OBSERVER_ERROR()]);
        $client = User::findOrFail($client_id);

        $client->update(["helped_showcasing" => $level]);
        $mailing = false;
        if($level == 0 && $client->email){
            Mail::to($client->email)->send(new PatronRejected($client->fresh()));
            $mailing = true;
        }

        if(Auth::id() == 1) return redirect()->route("dashboard")->with("toast", ["success", (($level == 2) ? "Wniosek przyjęty" : "Wniosek odrzucony").($mailing ? ", mail wysłany" : "")]);
        return redirect()->route("dashboard")->with("toast", ["success", "Wystawienie opinii odnotowane"]);
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
        $fieldName = Modal::where("name", "select-user-to-request")->first()
            ->fields[0][5]["selectData"]["fieldName"];
        $data = User::has("notes")
            ->get()
            ->map(fn ($u) => collect([
                "id" => $u->id,
                "name" => $u->notes->client_name,
                "email" => $u->notes->email,
                "phone" => $u->notes->phone,
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

        return view("components.shipyard.ui.lookup-results", compact(
            "data",
            "headings",
            "fieldName",
        ))->render();
    }

    public function lookupSongs()
    {
        $fieldName = Modal::where("name", "select-song-to-request")->first()
            ->fields[0][5]["selectData"]["fieldName"];
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
                "notes" => $s->notes
                    ? '<span '.Popper::pop(Markdown::parse($s->notes)).'>'.view("components.shipyard.app.icon", ["name" => model_field_icon("songs", "notes")]).'</span>'
                    : null,
            ]))
            ->values();
        $headings = collect([
            "ID",
            "Tytuł",
            "Wykonawca",
            "Linki",
            "Notatki",
        ]);

        return view("components.shipyard.ui.lookup-results", compact(
            "data",
            "headings",
            "fieldName",
        ))->render();
    }
    #endregion
}
