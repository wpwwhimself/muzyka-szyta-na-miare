<?php

namespace App\Http\Controllers;

use App\Mail\QuestAwaitingPayment;
use App\Mail\QuestAwaitingReview;
use App\Mail\QuestExpired;
use App\Mail\RequestAwaitingReview;
use App\Mail\RequestExpired;
use App\Models\Quest;
use App\Models\Request;
use App\Models\StatusChange;
use App\Models\Top10;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JanitorController extends Controller
{
    public $summary;
    public static $OPERATIONS = [
        "5_REMINDED" => "Przypomnienie o działaniu",
        "7_FORGOT" => "Brak reakcji",
        "15_REMINDED" => "Przypomnienie o działaniu",
        "17_FORGOT" => "Brak opinii",
        "17_UNPAID" => "Nieopłacone, ale zaakceptowane",
        "19_ALLGOOD" => "Brak uwag",
        "31_REMINDED" => "Przypomnienie o działaniu",
        "33_REMINDED" => "Przypomnienie o opłacie",
        "95_REMINDED" => "Przypomnienie o działaniu",
    ];

    public function getSummary(){
        return $this->summary;
    }
    private function clearSummary(){
        $this->summary = [];
    }
    private function addToSummary($procedure, $subject_type, $subject, $comment, $mailing = null){
        $this->summary[] = compact("procedure", "subject_type", "subject", "comment", "mailing");
        return $this->summary;
    }
    private function exportSummary(){
        Storage::put("janitor_log.json", json_encode($this->summary, JSON_PRETTY_PRINT));
    }

    //////////////////////////////////////////////////////////////////////////

    public function index(){
        $this->clearSummary();

        $this->re_quests_cleanup();
        $this->safe_cleanup();

        $this->top10_active_update();

        $this->exportSummary();
        return response()->json(["message" => "Sprzątacz wykonał swoją robotę"]);
    }

    //////////////////////////////////////////////////////////////////////////

    private function re_quests_cleanup(){
        /**
         * constants
         */
        foreach([
            "quest_reminder_time",
            "request_expired_after",
            "quest_expired_after",
            ] as $name){
            $$name = setting($name);
        }
        $request_reminder_time = floor($quest_reminder_time / 2);

        /**
         * expiring requests
         */
        $requests = Request::whereIn("status_id", [5, 95])
            ->where(function ($query) use ($request_expired_after){
                $query->where("updated_at", "<=", Carbon::now()->subDays($request_expired_after)->toDateString())
                ->orWhere("deadline", "<=", Carbon::today()->toDateString());
            })
            ->get();
        foreach($requests as $request){
            $request->update(["status_id" => 7]);
            $summaryEntry = [
                "procedure" => "re_quests",
                "subject_type" => "request",
                "subject" => $request->id,
                "comment" => "7_FORGOT",
            ];
            if($request->client?->email || $request->email){
                Mail::to($request->email ?? $request->client->email)->send(new RequestExpired($request->fresh()));
                BackController::newStatusLog($request->id, 7, "Brak reakcji", 1, 1);
                $summaryEntry["mailing"] = 1 + intval($request->client?->contact_preference == "email" || $request->contact_preference == "email");
            }else{
                BackController::newStatusLog($request->id, 7, "Brak reakcji", 1, null);
                $summaryEntry["mailing"] = 0;
            }
            $this->addToSummary(...$summaryEntry);
        }

        /**
         * expiring unreviewed quests
         */
        $quests = Quest::whereIn("status_id", [15, 31, 95])
            ->where(function($q){
                $q->whereHas('client', function($q){ $q->where('trust', '<', 1); })
                    ->orWhereHas('client', function($q){ $q->where('trust', 1); })->where("paid", true);
            })
            ->where(function($q) use ($quest_expired_after){
                $q->where(function($qq) use ($quest_expired_after){
                    $qq->where("paid", false)->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateString());
                })->orWhere(function($qq) use ($quest_expired_after){
                    $qq->where("paid", true)->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after / 2)->toDateString());
                });
            })
            ->get();
        foreach($quests as $quest){
            if ($quest->client->is_forgotten) continue;

            [$new_status, $new_comment] = $quest->paid ? [19, "19_ALLGOOD"] : [17, "17_FORGOT"];
            $quest->update(["status_id" => $new_status]);
            $summaryEntry = [
                "procedure" => "re_quests",
                "subject_type" => "quest",
                "subject" => $quest->id,
                "comment" => $new_comment,
            ];
            if($quest->client->email){
                Mail::to($quest->client->email)->send(new QuestExpired($quest->fresh(), "brak opinii"));
                BackController::newStatusLog($quest->id, $new_status, self::$OPERATIONS[$new_comment], 1, 1);
                $summaryEntry["mailing"] = 1 + intval($quest->client->contact_preference == "email");
            }else{
                BackController::newStatusLog($quest->id, $new_status, self::$OPERATIONS[$new_comment], 1, null);
                $summaryEntry["mailing"] = 0;
            }
            $this->addToSummary(...$summaryEntry);
        }

        /**
         * expiring accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)
            ->where("status_id", 19)
            ->whereHas('client', function($q){
                $q->where('trust', '<', 1);
            })
            ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateString())
            ->where(fn($q) => $q
                ->whereDate("delayed_payment", "<", Carbon::today()->subMonth())
                ->orWhereNull("delayed_payment"))
            ->get();
        foreach($quests as $quest){
            if ($quest->client->is_forgotten) continue;

            $quest->update(["status_id" => 17]);
            $quest->client->update(["trust" => -1]);
            $summaryEntry = [
                "procedure" => "re_quests",
                "subject_type" => "quest",
                "subject" => $quest->id,
                "comment" => "17_UNPAID",
            ];
            if($quest->client->email){
                Mail::to($quest->client->email)->send(new QuestExpired($quest->fresh(), "brak wpłaty"));
                BackController::newStatusLog($quest->id, 17, "Brak wpłaty", 1, 1);
                $summaryEntry["mailing"] = 1 + intval($quest->client->contact_preference == "email");
            }else{
                BackController::newStatusLog($quest->id, 17, "Brak wpłaty", 1, null);
                $summaryEntry["mailing"] = 0;
            }
            $this->addToSummary(...$summaryEntry);
        }

        /**
         * reminding clients about pending requests
         */
        $requests = Request::whereIn("status_id", [5, 95])->get();
        foreach($requests as $request){
            if(
                $request->updated_at->diffInDays(Carbon::now()) % $request_reminder_time == $request_reminder_time - 1
                &&
                !$request->updated_at->isToday()
                &&
                !$request->client?->is_forgotten
            ){
                $summaryEntry = [
                    "procedure" => "re_quests",
                    "subject_type" => "request",
                    "subject" => $request->id,
                    "comment" => $request->status_id."_REMINDED",
                ];
                if($request->client?->email ?? $request->email){
                    Mail::to($request->client?->email ?? $request->email)->send(new RequestAwaitingReview($request->fresh()));
                    StatusChange::where("re_quest_id", $request->id)->whereIn("new_status_id", [5, 95])->orderByDesc("date")->first()->increment("mail_sent");
                    $summaryEntry["mailing"] = 1 + intval(($request->client?->contact_preference ?? $request->contact_preference) == "email");
                }else{
                    $summaryEntry["mailing"] = 0;
                }
                $this->addToSummary(...$summaryEntry);
            }
        }

        /**
         * reminding clients about unreviewed quests
         */
        $quests = Quest::whereIn("status_id", [15, 31, 95])->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == $quest_reminder_time - 1
                &&
                !$quest->updated_at->isToday()
                &&
                !$quest->client->is_forgotten
            ){
                $summaryEntry = [
                    "procedure" => "re_quests",
                    "subject_type" => "quest",
                    "subject" => $quest->id,
                    "comment" => $quest->status_id."_REMINDED",
                ];
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingReview($quest->fresh()));
                    StatusChange::where("re_quest_id", $quest->id)->whereIn("new_status_id", [15, 31, 95])->orderByDesc("date")->first()->increment("mail_sent");
                    $summaryEntry["mailing"] = 1 + intval($quest->client->contact_preference == "email");
                }else{
                    $summaryEntry["mailing"] = 0;
                }
                $this->addToSummary(...$summaryEntry);
            }
        }

        /**
         * reminding clients about accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)
            ->where("status_id", 19)
            ->where(fn($q) => $q
                ->whereDate("delayed_payment", "<", Carbon::today())
                ->orWhereNull("delayed_payment")
            )->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == $quest_reminder_time - 1
                &&
                !$quest->updated_at->isToday()
                &&
                !$quest->client->is_forgotten
            ){
                $summaryEntry = [
                    "procedure" => "re_quests",
                    "subject_type" => "quest",
                    "subject" => $quest->id,
                    "comment" => "33_REMINDED",
                ];
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingPayment($quest->fresh()));
                    //status
                    $status = StatusChange::where("re_quest_id", $quest->id)->where("new_status_id", 33)->first();
                    if($status){
                        $status->increment("mail_sent");
                    }else{
                        BackController::newStatusLog($quest->id, 33, null, 1, true);
                    }
                    $summaryEntry["mailing"] = 1 + intval($quest->client->contact_preference == "email");
                }else{
                    $summaryEntry["mailing"] = 0;
                }
                $this->addToSummary(...$summaryEntry);
            }
        }
    }

    private function safe_cleanup(){
        $safes = Storage::disk()->directories("safe");
        $sizes = []; $times = [];

        foreach($safes as $safe){
            $files = Storage::files($safe);
            $size = 0;
            $modtime = 0;
            foreach($files as $file){
                $size += Storage::size($file);
                if(Storage::lastModified($file) > $modtime) $modtime = Storage::lastModified($file);
            }
            $modtime = new Carbon($modtime);

            if($modtime->diffInDays() >= setting("safe_old_enough")){
                Storage::deleteDirectory($safe);
                $this->addToSummary(
                    "safe",
                    "song",
                    preg_replace('/.*\/(.{4}).*/', '$1', $safe),
                    "Sejf wyczyszczony"
                );
            }
        }

    }

    private function top10_active_update()
    {
        Top10::where("type", "active")->delete();

        $most_active = User::has("questsRecent")
            ->with("questsRecent")
            ->withCount("questsRecent")
            ->orderByDesc("quests_recent_count")
            ->limit(10)
            ->get();
        
        foreach ($most_active as $user) {
            $user->top10()->create([
                "type" => "active",
            ]);
        }
    }
}
