<?php

namespace App\Http\Controllers;

use App\Mail\QuestAwaitingPayment;
use App\Mail\QuestAwaitingReview;
use App\Mail\QuestExpired;
use App\Mail\RequestExpired;
use App\Models\Quest;
use App\Models\Request;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JanitorController extends Controller
{
    public function index(){
        $summary = [];

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
            if($request->client?->email || $request->email){
                Mail::to($request->email ?? $request->client->email)->send(new RequestExpired($request));
                app("App\Http\Controllers\BackController")->statusHistory($request->id, 7, "brak reakcji", 1, 1);
                $summary[] = [
                    "re_quest" => $request, "is_request" => true,
                    "operation" => "Zapytanie wygaszone - brak reakcji - mail wysłany".(($request->client?->contact_preference == "email" || $request->contact_preference == "email") ? "" : ", ale WYMAGA KONTAKTU"),
                ];
            }else{
                app("App\Http\Controllers\BackController")->statusHistory($request->id, 7, "brak reakcji", 1, null);
                $summary[] = [
                    "re_quest" => $request, "is_request" => true,
                    "operation" => "Zapytanie wygaszone - brak reakcji - WYMAGA KONTAKTU",
                ];
            }
        }

        /**
         * expiring unreviewed quests
         */
        $quests = Quest::whereIn("status_id", [15, 95])
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
            [$new_status, $new_comment, $operation] = $quest->paid ? [19, "brak uwag", "Zlecenie zaakceptowane automatycznie"] : [17, "brak opinii", "Zlecenie wygaszone"];
            $quest->update(["status_id" => $new_status]);
            if($quest->client->email){
                Mail::to($quest->client->email)->send(new QuestExpired($quest, "brak opinii"));
                app("App\Http\Controllers\BackController")->statusHistory($quest->id, $new_status, $new_comment, 1, 1);
                $summary[] = [
                    "re_quest" => $quest, "is_request" => false,
                    "operation" => "$operation - $new_comment - mail wysłany".(($quest->client->contact_preference == "email") ? "" : ", ale WYMAGA KONTAKTU"),
                ];
            }else{
                app("App\Http\Controllers\BackController")->statusHistory($quest->id, $new_status, $new_comment, 1, null);
                $summary[] = [
                    "re_quest" => $quest, "is_request" => false,
                    "operation" => "$operation - $new_comment - WYMAGA KONTAKTU",
                ];
            }
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
            ->whereDate("delayed_payment", "<", Carbon::today()->subMonth())
            ->get();
        foreach($quests as $quest){
            $quest->update(["status_id" => 17]);
            $quest->client->update(["trust" => -1]);
            if($quest->client->email){
                Mail::to($quest->client->email)->send(new QuestExpired($quest, "brak wpłaty"));
                app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak wpłaty", 1, 1);
                $summary[] = [
                    "re_quest" => $quest, "is_request" => false,
                    "operation" => "Zlecenie wygaszone - nieopłacone, choć zaakceptowane - mail wysłany".(($quest->client->contact_preference == "email") ? "" : ", ale WYMAGA KONTAKTU"),
                ];
            }else{
                app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak wpłaty", 1, null);
                $summary[] = [
                    "re_quest" => $quest, "is_request" => false,
                    "operation" => "Zlecenie wygaszone - nieopłacone, choć zaakceptowane - WYMAGA KONTAKTU",
                ];
            }
        }

        /**
         * reminding clients about unreviewed quests
         */
        $quests = Quest::whereIn("status_id", [15, 95])->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == $quest_reminder_time - 1
                &&
                !$quest->updated_at->isToday()
            ){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingReview($quest));
                    StatusChange::where("re_quest_id", $quest->id)->whereIn("new_status_id", [15, 95])->orderByDesc("date")->first()->increment("mail_sent");
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o działaniu - mail wysłany".(($quest->client->contact_preference == "email") ? "" : ", ale WYMAGA KONTAKTU"),
                    ];
                }else{
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o działaniu - WYMAGA KONTAKTU",
                    ];
                }
            }
        }

        /**
         * reminding clients about accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)
            ->where("status_id", 19)
            ->whereDate("delayed_payment", "<", Carbon::today())
            ->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == $quest_reminder_time - 1
                &&
                !$quest->updated_at->isToday()
            ){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingPayment($quest));
                    //status
                    $status = StatusChange::where("re_quest_id", $quest->id)->where("new_status_id", 33)->first();
                    if($status){
                        $status->increment("mail_sent");
                    }else{
                        app("App\Http\Controllers\BackController")->statusHistory($quest->id, 33, null, 1, true);
                    }
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o opłacie - mail wysłany".(($quest->client->contact_preference == "email") ? "" : ", ale WYMAGA KONTAKTU"),
                    ];
                }else{
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o opłacie - WYMAGA KONTAKTU",
                    ];
                }
            }
        }

        /**
         * summary and report
         */
        Storage::put("janitor_log.json", json_encode($summary, JSON_PRETTY_PRINT));
        return "Report ready";
    }
}
