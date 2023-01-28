<?php

namespace App\Http\Controllers;

use App\Mail\MassPayment;
use App\Models\Client;
use App\Models\Quest;
use App\Models\Status;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StatsController extends Controller
{
    public function dashboard(){
        $big_summary = [
            "biznes kręci się od" => date_diff(date_create('2020-01-01'), date_create())->format("%y lat, %m miesięcy i %d dni"),
            "liczba skończonych questów" => Quest::where("status_id", 19)->count(),
            "zarobiono w sumie" => number_format(StatusChange::where("new_status_id", 32)->sum("comment"), 2, ",", " ")." zł",
        ];
        $clients_summary = [
            "łącznie" => Client::count(),
            "zaufanych" => Client::where("trust", 1)->count(),
            "krętaczy" => Client::where("trust", -1)->count(),
            "patronów" => Client::where("helped_showcasing", 2)->count(),
        ];
        $clients_counts = [
            "weterani (".VETERAN_FROM()."+)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, ">=", VETERAN_FROM())->count(),
            "biegli (4-".(VETERAN_FROM()-1).")" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [4, VETERAN_FROM()-1]);
            })->count(),
            "zainteresowani (2-3)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19)->selectRaw("count(*) as count")->havingBetween("count", [2, 4-1]);
            })->count(),
            "nowicjusze (1)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 1)->count(),
            "debiutanci (0)" => Client::whereHas("quests", function($q){
                $q->where("status_id", 19);
            }, 0)->count(),
        ];
        $new_clients = array_count_values(Client::whereDate("created_at", ">=", Carbon::now()->subYear())->get("created_at")->map(function($date){
            return($date->created_at->format("Y-m"));
        })->toArray());

        $last_month = [
            "nowych zleceń" => Quest::whereDate("created_at", ">", Carbon::today()->subMonth())->count(),
            "ukończonych zleceń" => StatusChange::where("new_status_id", 19)->whereDate("date", ">", Carbon::today()->subMonth())->count(),
            "debiutanckich zleceń" => Client::whereDate("created_at", ">", Carbon::today()->subMonth())->count(),
        ];

        $income = StatusChange::where("new_status_id", 32)
            ->whereDate("date", ">=", Carbon::now()->subYear())
            ->groupByRaw("YEAR(date), MONTH(date)")
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as date, sum(comment) as amount")
            ->pluck("amount", "date")
            ->toArray()
        ;

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact(
                "big_summary",
                "last_month",
                "income",
                "clients_summary", "clients_counts", "new_clients"
            ),
        ));
    }

    public function financeDashboard(){
        $unpaids_raw = Quest::where("paid", 0)
            ->whereNotIn("status_id", [17, 18])
            ->whereHas("client", function($query){
                $query->where("trust", ">", -1);
            })
            ->orderBy("quests.updated_at")
            ->get();
        $unpaids = [];
        if(count($unpaids_raw) > 0){
            foreach($unpaids_raw as $quest){
                $unpaids[$quest->client->id][] = $quest;
            };
        }

        $recent = StatusChange::where("new_status_id", 32)->orderByDesc("date")->limit(10)->get();
        foreach($recent as $i){
            $i->quest = Quest::find($i->re_quest_id);
            $i->new_status = Status::find($i->new_status_id);
        }

        return view(user_role().".finance", array_merge(
            ["title" => "Centrum Finansowe"],
            compact(
                "unpaids", "recent"
            ),
        ));
    }
    public function financePay(Request $rq){
        $quest_ids = array_keys($rq->except("_token"));

        $clients_quests = [];
        foreach($quest_ids as $id){
            $quest = Quest::find($id);

            // opłać zlecenia
            // na razie wpłaca całą kwotę //TODO podawanie konkretnych kwot
            app("App\Http\Controllers\BackController")->statusHistory($id, 32, $quest->price, $quest->client_id, $quest->client->isMailable());
            $quest->update(["paid" => (StatusChange::where(["new_status_id" => 32, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // zbierz zlecenia dla konkretnych adresatów
            $clients_quests[$quest->client_id][] = $quest;
        }

        // roześlij maile, jeśli można
        foreach($clients_quests as $client_id => $quests){
            $client = Client::find($client_id);
            if($client->isMailable()){
                Mail::to($quest->client->email)->send(new MassPayment($quests));
            }
        }

        return back()->with("success", "Zlecenia opłacone");
    }
}
