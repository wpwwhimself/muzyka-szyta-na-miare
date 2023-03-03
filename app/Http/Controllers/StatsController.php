<?php

namespace App\Http\Controllers;

use App\Mail\MassPayment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quest;
use App\Models\Status;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StatsController extends Controller
{
    public function dashboard(){
        $stats = json_decode(Storage::get("/stats.json"));
        $stats->today = Carbon::parse($stats->today)->diffForHumans();

        return view(user_role().".stats", array_merge(
            ["title" => "GUS"],
            compact("stats"),
        ));
    }
    public function statsImport(Request $rq){
        $rq->file("json")->storeAs("/", "stats.json");
        return back()->with("success", "Dane zaktualizowane");
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
            app("App\Http\Controllers\BackController")->statusHistory(
                $id,
                32,
                $quest->price - $quest->payments->sum("comment"),
                $quest->client_id,
                $quest->client->isMailable()
            );
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

    public function invoice($id){
        $invoice = Invoice::findOrFail($id);
        //TODO dane fakturowicza

        return view(user_role().".invoice", array_merge(
            ["title" => "Faktura nr ".$invoice->fullCode()],
            compact(
                "invoice", //dane fakturowicza
            ),
        ));
    }
}
