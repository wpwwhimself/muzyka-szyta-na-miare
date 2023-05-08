<?php

namespace App\Http\Controllers;

use App\Mail\MassPayment;
use App\Models\Client;
use App\Models\Cost;
use App\Models\CostType;
use App\Models\Invoice;
use App\Models\Quest;
use App\Models\Request as ModelsRequest;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\Status;
use App\Models\StatusChange;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function dashboard(){
        //helpers
        $quest_pricings = array_combine(
            DB::table("prices")->orderBy("indicator")->pluck("service")->toArray(),
            array_map(
                fn($el) => Song::where("price_code", "regexp", $el)->count(),
                DB::table("prices")->orderBy("indicator")->pluck("indicator")->toArray()
            )
        );
        arsort($quest_pricings);
        $recent_income = StatusChange::where("new_status_id", 32)
            ->whereDate("date", ">=", Carbon::today()->subYear()->firstOfMonth())
            ->selectRaw("DATE_FORMAT(date, '%y-%m') as month,
                sum(comment) as sum,
                round(avg(comment), 2) as mean")
            ->groupBy("month")
            ->orderBy("month")
            ->get();
        $recent_costs = Cost::whereDate("created_at", ">=", Carbon::today()->subYear()->firstOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%y-%m') as month,
                sum(amount) as sum,
                round(avg(amount), 2) as mean")
            ->groupBy("month")
            ->orderBy("month")
            ->get();
        $recent_gross = collect($recent_income->pluck("sum", "month"))
            ->mergeRecursive($recent_costs->pluck("sum", "month"))
            ->mapWithKeys(fn($val, $key) => [$key => $val[0] - $val[1]]);
        $finances_total = [
            "przychody" => StatusChange::where("new_status_id", 32)
                ->whereDate("date", ">=", Carbon::today()->subYear())
                ->sum("comment"),
            "koszty" => Cost::whereDate("created_at", ">=", Carbon::today()->subYear())
                ->sum("amount"),
        ];
        $finances_total_last_year = [
            "przychody" => StatusChange::where("new_status_id", 32)
                ->whereDate("date", ">=", Carbon::today()->subYears(2))->whereDate("date", "<", Carbon::today()->subYear())
                ->sum("comment"),
            "koszty" => Cost::whereDate("created_at", ">=", Carbon::today()->subYears(2))->whereDate("created_at", "<", Carbon::today()->subYear())
                ->sum("amount"),
        ];
        $finances_total["dochody"] = $finances_total["przychody"] - $finances_total["koszty"];
        $finances_total_last_year["dochody"] = $finances_total_last_year["przychody"] - $finances_total_last_year["koszty"];
        $client_exp_raw = Client::withCount("questsDone")
            ->pluck("quests_done_count")
            ->countBy()
            ->toArray();
        function client_exp_tally($raw, $low = 0, $high = INF){
            $ret = 0;
            foreach($raw as $key => $val){
                if($key >= $low && $key <= $high){
                    $ret += $val;
                }
            }
            return $ret;
        }
        foreach(DB::table(DB::raw("(SELECT distinct `date`, hard_deadline, datediff(hard_deadline, `date`) as difference
                FROM status_changes
                LEFT JOIN quests on re_quest_id = quests.id
                WHERE new_status_id = 19
                    AND hard_deadline IS NOT NULL
                ORDER BY re_quest_id, status_changes.id DESC) as x"))
            ->selectRaw("difference, count(*) as count")
            ->groupBy("difference")
            ->pluck("count", "difference") as $deadline => $count){
                $label = ($deadline <= -7) ? "<= -7" : (
                    ($deadline >= 7) ? ">= 7" : $deadline
                );
                $hard_deadline_count[$label] ??= 0;
                $hard_deadline_count[$label] += $count;
            }

        $stats = [
            "summary" => [
                "general" => [
                    "biznes kręci się od" => Carbon::createFromDate(2020, 1, 1)->diff(Carbon::now())->format("%yl %mm %dd"),
                    "skończone questy" => Quest::where("status_id", 19)->count(),
                    "poznani klienci" => Client::count(),
                    "zarobki w sumie" => as_pln(StatusChange::where("new_status_id", 32)->sum("comment"), 2, ",", " "),
                ],
                "quest_types" => [
                    "split" => DB::table("quests")
                        ->selectRaw("type, count(*) as count")
                        ->join("quest_types", DB::raw("left(quests.song_id, 1)"), "quest_types.code")
                        ->groupBy("type")
                        ->orderByDesc("count")
                        ->pluck("count", "type"),
                    "total" => Quest::count(),
                ],
                "quest_pricings" => [
                    "split" => array_slice($quest_pricings, 0, 6),
                    "total" => Song::where("price_code", "not regexp", "^\d*\.\d*$")->count(),
                ],
            ],
            "quests" => [
                "recent" => [
                    "main" => [
                        "nowe" => Quest::where("created_at", ">=", Carbon::today()->subMonths(1))->count(),
                        "ukończone" => 1,
                        "debiutanckie" => 1,
                        "max poprawek" => 1,
                    ],
                    "compared_to" => [
                        "nowe" => Quest::whereBetween("created_at", [Carbon::today()->subMonths(2), Carbon::today()->subMonths(1)])->count(),
                        "ukończone" => 1,
                        "debiutanckie" => 1,
                        "max poprawek" => 1,
                    ],
                ],
                "statuses" => [
                    "split" => Quest::join("statuses", "statuses.id", "status_id")
                        ->groupBy("status_name", "status_id")
                        ->orderBy("status_id")
                        ->selectRaw("status_id, status_name, count(*) as count")
                        ->pluck("count", "status_name"),
                    "total" => Quest::count(),
                ],
                "corrections" => [
                    "rows" => StatusChange::whereIn("new_status_id", [16, 26])
                        ->groupBy("re_quest_id")
                        ->orderByDesc("Liczba poprawek")
                        ->limit(5)
                        ->join("quests", "re_quest_id", "quests.id", "left")
                        ->join("clients", "quests.client_id", "clients.id", "left")
                        ->join("songs", "quests.song_id", "songs.id", "left")
                        ->join("genres", "songs.genre_id", "genres.id", "left")
                        ->selectRaw("re_quest_id as 'ID zlecenia',
                            clients.client_name as 'Klient',
                            songs.title as 'Tytuł utworu',
                            genres.name as 'Gatunek utworu',
                            count(*) as 'Liczba poprawek'")
                        ->get(),
                    "footer" => DB::table(DB::raw("(SELECT re_quest_id, count(*) as count
                            FROM status_changes
                            WHERE new_status_id in (16, 26) AND date > '2023-01-01'
                            GROUP BY re_quest_id) as x"))
                        ->selectRaw("'średnio poprawek' as label, avg(count) as aver")
                        ->pluck("aver", "label"),
                ],
                "deadlines" => [
                    "soft" => [
                        "split" => DB::table(DB::raw("(SELECT distinct `date`, deadline, datediff(deadline, `date`) as difference
                                FROM status_changes
                                LEFT JOIN quests ON re_quest_id = quests.id
                                WHERE new_status_id = 15 AND deadline is not NULL
                                GROUP BY re_quest_id
                                ORDER BY re_quest_id, `date`) as x "))
                            ->selectRaw("difference, count(*) as count")
                            ->groupBy("difference")
                            ->pluck("count", "difference"),
                        "total" => StatusChange::where("new_status_id", 15)
                            ->distinct("re_quest_id")
                            ->join("quests", "re_quest_id", "=", "quests.id", "left")
                            ->select("deadline")
                            ->whereNotNull("deadline")
                            ->count(),
                    ],
                    "hard" => $hard_deadline_count,
                ],
            ],
            "clients" => [
                "summary" => [
                    "split" => [
                        "zaufani" => Client::where("trust", 1)->count(),
                        "krętacze" => Client::where("trust", -1)->count(),
                        "patroni" => Client::where("helped_showcasing", 2)->count(),
                        "bez zleceń" => Client::withCount("questsDone")
                            ->having("quests_done_count", 0)
                            ->count(),
                        "kobiety" => Client::all()
                            ->filter(fn($client) => $client->isWoman())
                            ->count(),
                    ],
                    "total" => Client::all()->count(),
                ],
                "exp" => [
                    "split" => [
                        "weterani (".VETERAN_FROM()."+)" => client_exp_tally($client_exp_raw, VETERAN_FROM()),
                        "biegli (4-".(VETERAN_FROM()-1).")" => client_exp_tally($client_exp_raw, 4, VETERAN_FROM()-1),
                        "zainteresowani (2-3)" => client_exp_tally($client_exp_raw, 2, 3),
                        "nowicjusze (1)" => client_exp_tally($client_exp_raw, 1, 1),
                    ],
                    "total" => Client::all()->count(),
                ],
                "new" => Client::whereDate("created_at", ">=", Carbon::today()->subYear()->firstOfMonth())
                    ->selectRaw("DATE_FORMAT(created_at, '%y-%m') as month,
                        count(*) as count")
                    ->groupBy("month")
                    ->orderBy("month")
                    ->pluck("count", "month"),
                "pickiness" => [
                    "high" => [
                        "rows" => Client::all()
                            ->sortByDesc("pickiness")
                            ->take(5)
                            ->map(fn($item, $key) => [
                                "Nazwisko" => $item->client_name,
                                "Wybredność" => $item->pickiness * 100 . "%",
                            ])
                            ->values(),
                    ],
                    "low" => [
                        "rows" => Client::withCount("questsDone")
                            ->where("trust", ">", -1)
                            ->having("quests_done_count", ">", 0)
                            ->join("quests", "client_id", "clients.id")
                            ->orderByDesc("quests.updated_at")
                            ->distinct("client_name")
                            ->get()
                            ->sortBy("pickiness")
                            ->take(5)
                            ->map(fn($item, $key) => [
                                "Nazwisko" => $item->client_name,
                                "Wybredność" => $item->pickiness * 100 . "%",
                            ])
                            ->values(0),
                    ],
                ]
            ],
            "finances" => [
                "income" => $recent_income->pluck("sum", "month"),
                "prop" => $recent_income->pluck("mean", "month"),
                "costs" => $recent_costs->pluck("sum", "month"),
                "gross" => $recent_gross,
                "total" => [
                    "main" => $finances_total,
                    "compared_to" => $finances_total_last_year,
                ]
            ],
            "songs" => [
                "time_summary" => [
                    "średnio na całość" => DB::table(DB::raw("(".SongWorkTime::groupBy("song_id")
                            ->selectRaw("sec_to_time(sum(time_to_sec(time_spent))) as sum, song_id")
                            ->toSql().") as x"))
                        ->selectRaw("date_format(sec_to_time(avg(time_to_sec(sum))), '%k:%i') as mean")
                        ->value("mean"),
                    "średnio elementów" => DB::table(DB::raw("(".SongWorkTime::groupBy("song_id")
                            ->selectRaw("count(song_id) as count")
                            ->toSql().") as x"))
                        ->where("count", ">", 1)
                        ->average("count"),
                ],
                "time_genres" => [
                    "main" => DB::table(DB::raw("(".SongWorkTime::join("songs", "song_id", "songs.id", "left")
                            ->groupBy(["song_id", "genre_id"])
                            ->selectRaw("song_id, genre_id, sec_to_time(sum(time_to_sec(time_spent))) as time_spent")
                            ->toSql().") as x"))
                        ->groupBy("genre_id")
                        ->join("genres", "genre_id", "genres.id")
                        ->selectRaw("name, date_format(sec_to_time(avg(time_to_sec(time_spent))), '%k:%i') as mean")
                        ->orderByDesc("mean")
                        ->pluck("mean", "name"),
                    // TODO naprawić
                    // "difference" => DB::table(DB::raw("(".SongWorkTime::join("songs", "song_id", "songs.id", "left")
                    //         ->whereDate("since", "<", Carbon::today()->subMonth())
                    //         ->groupBy(["song_id", "genre_id"])
                    //         ->selectRaw("song_id, genre_id, sec_to_time(sum(time_to_sec(time_spent))) as time_spent")
                    //         ->toSql().") as x"))
                    //     ->groupBy("genre_id")
                    //     ->join("genres", "genre_id", "genres.id")
                    //     ->selectRaw("name, date_format(sec_to_time(avg(time_to_sec(time_spent))), '%k:%i') as mean")
                    //     ->orderByDesc("mean")
                    //     ->pluck("mean", "name"),
                ],
            ],
        ];

        $stats = json_decode(json_encode($stats));
        // dd($stats->songs->time_genres->main);

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

        return view(user_role().".invoice", array_merge(
            ["title" => "Faktura nr ".$invoice->fullCode()],
            compact("invoice"),
        ));
    }
    public function invoiceVisibility(Request $rq){
        Invoice::find($rq->id)->update(["visible" => $rq->visible]);

        return back()->with("success", $rq->visible ? "Faktura widoczna" : "Faktura schowana");
    }
    public function invoiceAdd(Request $rq){
        $quest = Quest::find($rq->quest_id);

        Invoice::create([
            "quest_id" => $quest->id,
            "primary" => ($quest->allInvoices()->count() == 0),
            "visible" => false,
            "amount" => $quest->price - $quest->allInvoices()->sum("paid"),
            "paid" => ($quest->paid ? $quest->price - $quest->allInvoices()->sum("paid") : 0),
            "payer_name" => $rq->payer_name,
            "payer_title" => $rq->payer_title,
            "payer_address" => $rq->payer_address,
            "payer_nip" => $rq->payer_nip,
            "payer_regon" => $rq->payer_regon,
            "payer_email" => $rq->payer_email,
            "payer_phone" => $rq->payer_phone,
        ]);

        return back()->with("success", "Dokument utworzony");
    }

    public function costs(){
        $costs = Cost::orderByDesc("created_at")->paginate(25);
        $types = CostType::all()->pluck("name", "id");

        return view(user_role().".costs", array_merge(
            ["title" => "Lista kosztów"],
            compact("costs", "types"),
        ));
    }
    public function modCost(Request $rq){
        $fields = [
            "cost_type_id" => $rq->cost_type_id,
            "desc" => $rq->desc,
            "amount" => $rq->amount,
        ];
        if($rq->id) Cost::find($rq->id)->update($fields);
        else Cost::create($fields);

        return back()->with("success", "Gotowe");
    }
    public function costTypes(){
        $types = CostType::all();

        return view(user_role().".cost-types", array_merge(
            ["title" => "Typy kosztów"],
            compact("types"),
        ));
    }
    public function modCostType(Request $rq){
        $fields = ["name" => $rq->name, "desc" => $rq->desc];
        if($rq->id) CostType::find($rq->id)->update($fields);
        else CostType::create($fields);

        return back()->with("success", "Gotowe");
    }

    public function fileSizeReport(){
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
            $sizes[$safe] = $size;
            $times[$safe] = new Carbon($modtime);
        }
        arsort($sizes);

        return view(user_role().".file-size-report", array_merge(
            ["title" => "Raport zajętości serwera"],
            compact(
                "sizes", "times",
            ),
        ));
    }

    public function questsCalendar(){
        $calendar_length = max(
            7,
            Quest::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays() + 2,
            ModelsRequest::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays() + 2,
        );

        return view(user_role().".quests-calendar", array_merge(
            ["title" => "Grafik zleceń"],
            compact(
                "calendar_length"
            ),
        ));
    }
}
