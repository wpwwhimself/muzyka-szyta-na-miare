<?php

use App\Mail\QuestAwaitingPayment;
use App\Mail\QuestAwaitingReview;
use App\Models\Client;
use App\Models\Quest;
use App\Models\Request;
use App\Models\StatusChange;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * constants
 */

foreach([
  "quest_reminder_time",
  "request_expired_after"
  ] as $name){
  $$name = DB::table("settings")->where("setting_name", $name)->value("value_str");
}

/**
 * expiring requests
 */
$requests = Request::where("status_id", 5)
  ->where("updated_at", "<=", Carbon::now()->subDays($request_expired_after)->toDateTimeString())
  ->get();
foreach($requests as $request){
  $request->update(["status_id", 7]);
  app("App\Http\Controllers\BackController")->statusHistory($request->id, 7, "wygaszono z powodu braku opinii", 1, null);
}

/**
 * expiring unreviewed quests
 */
$quests = Quest::where("status_id", 15)
  ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateTimeString())
  ->get();
foreach($quests as $quest){
  $quest->update(["status_id", 17]);
  app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "wygaszono z powodu braku opinii", 1, null);
}

/**
 * expiring accepted but unpaid quests
 */
$quests = Quest::where("paid", 0)
  ->where("status_id", 19)
  ->where("updated_at", "<=", Carbon::now()->subDays($quest_expired_after)->toDateTimeString())
  ->get();
foreach($quests as $quest){
  $quest->update(["status_id", 17]);
  $quest->client->update("trust", -1);
  app("App\Http\Controllers\BackController")->statusHistory($quest->id, 17, "wygaszono z powodu braku wpłaty", 1, null);
}

/**
 * reminding clients about unreviewed quests
 */
$quests = Quest::where("status_id", 15)->get();
foreach($quests as $quest){
  if(
    $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == 0
    &&
    !$quest->updated_at->isToday()
  ){
    if($quest->client->email){
      Mail::to($quest->client->email)->send(new QuestAwaitingReview($quest));
      StatusChange::where("re_quest_id", $quest->id)->where("status_id", 15)->orderByDesc("date")->first()->increnment("mail_sent");
    }
  }
}

/**
 * reminding clients about accepted but unpaid quests
 */
$quests = Quest::where("paid", 0)->where("status_id", 19)->get();
foreach($quests as $quest){
  if(
    $quest->updated_at->diffInDays(Carbon::now()) % $quest_reminder_time == 0
    &&
    !$quest->updated_at->isToday()
  ){
    if($quest->client->email){
      Mail::to($quest->client->email)->send(new QuestAwaitingPayment($quest));
    }
  }
}
