<?php

namespace App\Http\Controllers;

use App\Mail\PatronRejected;
use App\Models\Client;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\Status;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

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
            ->orderBy("deadline")
            ->orderByRaw("case when price_code_override regexp 'z' and status_id in (11, 12, 16, 26, 96) then 0 else 1 end")
            ->orderByRaw("paid desc")
            ->orderBy("created_at")
            ->get();
        $quests_review = Quest::whereNotIn("status_id", [17, 18, 19])
            ->whereNotIn("status_id", STATUSES_WAITING_FOR_ME())
            ->orderByDesc("deadline")
            ->orderBy("created_at")
            ->get();

        if(!is_archmage()){
            $requests = $requests->where("client_id", $client->id);
            $quests_total = Auth::user()->client->exp;
            $unpaids = Quest::where("client_id", Auth::id())
                ->whereNotIn("status_id", [18])
                ->where("paid", 0)
                ->get();
        }else{
            $recent = StatusChange::whereNotIn("new_status_id", [9, 32, 34])
                ->where(fn($q) => $q
                    ->where("changed_by", "!=", 1)
                    ->orWhereNull("changed_by")
                )
                ->orderByDesc("date")
                ->limit(7)
                ->get();
            foreach($recent as $change){
                $change->is_request = is_request($change->re_quest_id);
                $change->re_quest = ($change->is_request) ?
                    Request::find($change->re_quest_id) :
                    Quest::find($change->re_quest_id);
                $change->new_status = Status::find($change->new_status_id);
            }
            $patrons_adepts = Client::where("helped_showcasing", 1)->get();
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
        $requests = $requests->get();

        return view(user_role().".dashboard", array_merge(
            [
                "title" => (is_archmage())
                ? (Auth::id() == 1 ? "Szpica arcymaga" : "WITAJ, OBSERWATORZE")
                : "Pulpit"
            ],
            compact("quests_ongoing", "quests_review", "requests"),
            (isset($quests_total) ? compact("quests_total") : []),
            (isset($patrons_adepts) ? compact("patrons_adepts") : []),
            (isset($unpaids) ? compact("unpaids") : []),
            (isset($janitor_log) ? compact("janitor_log") : []),
            (isset($recent) ? compact("recent") : []),
            (isset($showcases_missing) ? compact("showcases_missing") : []),
        ));
    }

    public function prices(){
        $prices = DB::table("prices")->get();

        $discount = (is_archmage()) ? null : (
            (Auth::user()->client->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
            +
            (Auth::user()->client->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
        );

        $clients = [];
        if (is_archmage()) {
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = _ct_("$client[client_name] «$client[id]»");
            }
        }

        $quest_types = QuestType::all()->pluck("type", "id")->toArray();
        $minimal_prices = array_combine($quest_types, QUEST_MINIMAL_PRICES());

        return view(user_role().".prices", array_merge(
            ["title" => "Cennik"],
            compact("prices", "discount", "minimal_prices", "clients")
        ));
    }

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
        if(Auth::id() === 0) return redirect()->route("dashboard")->with("error", OBSERVER_ERROR());
        $client = Client::findOrFail($client_id);

        $client->update(["helped_showcasing" => $level]);
        $mailing = false;
        if($level == 0 && $client->email){
            Mail::to($client->email)->send(new PatronRejected($client->fresh()));
            $mailing = true;
        }

        if(Auth::id() == 1) return redirect()->route("dashboard")->with("success", (($level == 2) ? "Wniosek przyjęty" : "Wniosek odrzucony").($mailing ? ", mail wysłany" : ""));
        return redirect()->route("dashboard")->with("success", "Wystawienie opinii odnotowane");
    }

    public function ppp($page = "0-index"){
        $titles = [];
        foreach(File::allFiles(resource_path("views/doc")) as $key => $ttl){
            $titles[$key] = preg_replace('/(.*)doc[\/\\\](.*)\.blade\.php/', "$2", $ttl);
        }

        return view(user_role().".ppp", array_merge(
            ["title" => "Poradnik Przyszłych Pokoleń"],
            compact("page", "titles")
        ));
    }

    public function settings(){
        $settings = DB::table("settings")->get();

        return view(user_role().".settings", array_merge(
            ["title" => "Ustawienia"],
            compact("settings")
        ));
    }

    ////////////////////////////////////////////

    public function updateSetting(HttpRequest $rq){
        if(Auth::id() != 1) return;
        foreach ($rq->except("_token") as $key => $value) {
            DB::table("settings")
                ->where("setting_name", $key)
                ->update(["value_str" => $value])
            ;
        }
        return back()->with("success", "Ustawienia zaktualizowane");
    }
}
