<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageJanitorReport;
use App\Mail\QuestAwaitingPayment;
use App\Mail\QuestAwaitingReview;
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
        $requests = Request::where("status_id", 5)
            ->where(function ($query) use ($request_expired_after){
                $query->where("updated_at", "<=", Carbon::now()->subDays($request_expired_after)->toDateString())
                ->orWhere("deadline", "<=", Carbon::today()->toDateString());
            })
            ->get();
        foreach($requests as $request){
            $request->update(["status_id" => 7]);
            app("App\Http\Controllers\BackController")->statusHistory($request->id, 7, "brak reakcji", 1, null);
            $summary[] = [
                "re_quest" => $request, "is_request" => true,
                "operation" => "Zapytanie wygaszone - brak reakcji",
            ];
        }

        /**
         * expiring unreviewed quests
         */
        $quests = Quest::where("status_id", 15)
            ->whereHas('client', function($q){
                $q->where('trust', '<', 1);
            })
            ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateString())
            ->get();
        foreach($quests as $quest){
            $quest->update(["status_id" => 17]);
            app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak opinii", 1, null);
            $summary[] = [
                "re_quest" => $quest, "is_request" => false,
                "operation" => "Zlecenie wygaszone - brak opinii",
            ];
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
            ->get();
        foreach($quests as $quest){
            $quest->update(["status_id" => 17]);
            $quest->client->update(["trust" => -1]);
            app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "brak wpłaty", 1, null);
            $summary[] = [
                "re_quest" => $quest, "is_request" => false,
                "operation" => "Zlecenie wygaszone - nieopłacone, choć zaakceptowane",
            ];
        }

        /**
         * reminding clients about unreviewed quests
         */
        $quests = Quest::where("status_id", 15)->get();
        foreach($quests as $quest){
            if(
                $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == $quest_reminder_time - 1
                &&
                !$quest->updated_at->isToday()
            ){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new QuestAwaitingReview($quest));
                    StatusChange::where("re_quest_id", $quest->id)->where("new_status_id", 15)->orderByDesc("date")->first()->increment("mail_sent");
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o recenzji - mail wysłany",
                    ];
                }else{
                    $summary[] = [
                        "re_quest" => $quest, "is_request" => false,
                        "operation" => "Przypomnienie o recenzji - WYMAGA KONTAKTU",
                    ];
                }
            }
        }

        /**
         * reminding clients about accepted but unpaid quests
         */
        $quests = Quest::where("paid", 0)->where("status_id", 19)->get();
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
                        "operation" => "Przypomnienie o opłacie - mail wysłany",
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
        if(count($summary) > 0){
            // Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageJanitorReport($summary));
            // return "Raport wysłany";
        }
        Storage::put("janitor_log.json", json_encode($summary, JSON_PRETTY_PRINT));
        return "Report ready";
    }
}
