<?php

namespace App\Http\Controllers;

use App\Mail\MassPayment;
use App\Mail\PaymentReturned;
use App\Models\CalendarFreeDay;
use App\Models\Cost;
use App\Models\CostType;
use App\Models\GigPriceDefault;
use App\Models\GigPricePlace;
use App\Models\GigPriceRate;
use App\Models\Invoice;
use App\Models\InvoiceQuest;
use App\Models\Quest;
use App\Models\Request as ModelsRequest;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\Top10;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->where("cost_type_id", "<>", 3)
            ->selectRaw("DATE_FORMAT(created_at, '%y-%m') as month,
                sum(amount) as sum,
                round(avg(amount), 2) as mean")
            ->groupBy("month")
            ->orderBy("month")
            ->get();
        $recent_income = $recent_income->pluck("sum", "month")->mergeRecursive($recent_income->pluck("mean", "month"));
        $recent_costs = $recent_costs->pluck("sum", "month")->mergeRecursive(($recent_costs)->pluck("mean", "month"));
        foreach($recent_income->mergeRecursive($recent_costs)->filter(fn($el) => count($el) < 4) as $month => $value){
            $recent_income = $recent_income->union([$month => [0, 0]]);
            $recent_costs = $recent_costs->union([$month => [0, 0]]);
        }
        $recent_income = $recent_income->sortKeys();
        $recent_costs = $recent_costs->sortKeys();
        $recent_gross = $recent_income->mergeRecursive($recent_costs)
            ->mapWithKeys(fn($val, $month) => [$month => $val[0] - $val[2]]);
        $finances_total = [
            "przychody" => StatusChange::where("new_status_id", 32)
                ->whereDate("date", ">=", Carbon::today()->subYear())
                ->sum("comment"),
            "koszty" => Cost::whereDate("created_at", ">=", Carbon::today()->subYear())
                ->where("cost_type_id", "<>", 3)
                ->sum("amount"),
        ];
        $finances_total_last_year = [
            "przychody" => StatusChange::where("new_status_id", 32)
                ->whereDate("date", ">=", Carbon::today()->subYears(2))->whereDate("date", "<", Carbon::today()->subYear())
                ->sum("comment"),
            "koszty" => Cost::whereDate("created_at", ">=", Carbon::today()->subYears(2))->whereDate("created_at", "<", Carbon::today()->subYear())
                ->where("cost_type_id", "<>", 3)
                ->sum("amount"),
        ];
        $finances_total["dochody"] = $finances_total["przychody"] - $finances_total["koszty"];
        $finances_total_last_year["dochody"] = $finances_total_last_year["przychody"] - $finances_total_last_year["koszty"];
        $recent_income_alltime = StatusChange::where("new_status_id", 32)
            ->whereDate("date", ">=", "2020-01-01")
            ->selectRaw("DATE_FORMAT(date, '%y-') as year,
                sum(comment) as sum,
                round(avg(comment), 2) as mean")
            ->groupBy("year")
            ->orderBy("year")
            ->get();
        $recent_costs_alltime = Cost::selectRaw("DATE_FORMAT(created_at, '%y-') as year,
                sum(amount) as sum,
                round(avg(amount), 2) as mean")
            ->whereDate("created_at", ">=", "2020-01-01")
            ->where("cost_type_id", "<>", 3)
            ->groupBy("year")
            ->orderBy("year")
            ->get();
        $recent_income_alltime = $recent_income_alltime->pluck("sum", "year")->mergeRecursive($recent_income_alltime->pluck("mean", "year"));
        $recent_costs_alltime = $recent_costs_alltime->pluck("sum", "year")->mergeRecursive(($recent_costs_alltime)->pluck("mean", "year"));
        foreach($recent_income_alltime->mergeRecursive($recent_costs_alltime)->filter(fn($el) => count($el) < 4) as $year => $value){
            $recent_income_alltime = $recent_income_alltime->union([$year => [0, 0]]);
            $recent_costs_alltime = $recent_costs_alltime->union([$year => [0, 0]]);
        }
        $recent_income_alltime = $recent_income_alltime->sortKeys();
        $recent_costs_alltime = $recent_costs_alltime->sortKeys();
        $recent_gross_alltime = collect($recent_income_alltime)
            ->mergeRecursive($recent_costs_alltime)
            ->mapWithKeys(fn($val, $key) => [$key => $val[0] - $val[2]]);
        $client_exp_raw = User::withCount("questsDone")
            ->pluck("quests_done_count", "client_name")
            ->mergeRecursive(User::all()->pluck("extra_exp", "client_name"))
            ->mapWithKeys(fn($val, $key) => [$key => $val[0] + $val[1]])
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

        // soft deadlines
        foreach(DB::table(DB::raw("(SELECT distinct `date`, deadline, datediff(deadline, `date`) as difference
                FROM status_changes
                LEFT JOIN quests ON re_quest_id = quests.id
                WHERE new_status_id = 15 AND deadline is not NULL
                GROUP BY re_quest_id
                ORDER BY re_quest_id, `date`) as x "))
            ->selectRaw("difference, count(*) as count")
            ->groupBy("difference")
            ->orderBy("difference")
            ->pluck("count", "difference") as $deadline => $count){
                $label = ($deadline <= -1) ? "<= -1" : (
                    ($deadline >= 7) ? ">= 7" : $deadline
                );
                $soft_deadline_count[$label] ??= 0;
                $soft_deadline_count[$label] += $count;
            }

        // hard deadlines
        foreach(DB::table(DB::raw("(SELECT distinct `date`, hard_deadline, datediff(hard_deadline, `date`) as difference
                FROM status_changes
                LEFT JOIN quests on re_quest_id = quests.id
                WHERE new_status_id = 19
                    AND hard_deadline IS NOT NULL
                ORDER BY re_quest_id, status_changes.id DESC) as x"))
            ->selectRaw("difference, count(*) as count")
            ->groupBy("difference")
            ->orderBy("difference")
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
                    "biznes kręci się od" => BEGINNING()->diff(Carbon::now())->format("%yl %mm %dd"),
                    "skończone questy" => Quest::where("status_id", 19)->count(),
                    "poznani klienci" => User::count(),
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
                "income_total" => $recent_gross_alltime,
            ],
            "quests" => [
                "recent" => [
                    "main" => [
                        "nowe" => Quest::where("created_at", ">=", Carbon::today()->subMonths(1))->count(),
                        "ukończone" => Quest::where("updated_at", ">=", Carbon::today()->subMonths(1))->where("status_id", 19)->count(),
                        "debiutanckie" => User::where("created_at", ">=", Carbon::today()->subMonths(1))->count(),
                        "max poprawek" => StatusChange::where("date", ">=", Carbon::today()->subMonths(1))->whereIn("new_status_id", [16, 26])->groupBy("re_quest_id")->selectRaw("count(*) as count")->orderByDesc("count")->limit(1)->value("count"),
                    ],
                    "compared_to" => [
                        "nowe" => Quest::whereBetween("created_at", [Carbon::today()->subMonths(2), Carbon::today()->subMonths(1)])->count(),
                        "ukończone" => Quest::whereBetween("updated_at", [Carbon::today()->subMonths(2), Carbon::today()->subMonths(1)])->where("status_id", 19)->count(),
                        "debiutanckie" => User::whereBetween("created_at", [Carbon::today()->subMonths(2), Carbon::today()->subMonths(1)])->count(),
                        "max poprawek" => StatusChange::whereBetween("date", [Carbon::today()->subMonths(2), Carbon::today()->subMonths(1)])->whereIn("new_status_id", [16, 26])->groupBy("re_quest_id")->selectRaw("count(*) as count")->orderByDesc("count")->limit(1)->value("count"),
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
                        ->limit(10)
                        ->join("quests", "re_quest_id", "quests.id", "left")
                        ->join("users", "quests.client_id", "users.id", "left")
                        ->join("songs", "quests.song_id", "songs.id", "left")
                        ->join("genres", "songs.genre_id", "genres.id", "left")
                        ->selectRaw("re_quest_id as 'ID zlecenia',
                            users.client_name as 'Klient',
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
                    "soft" => $soft_deadline_count,
                    "hard" => $hard_deadline_count,
                ],
                "requests" => [
                    "split" => ModelsRequest::join("statuses", "statuses.id", "status_id")
                        ->groupBy("status_name", "status_id")
                        ->orderByDesc("status_id")
                        ->selectRaw("status_id, status_name, count(*) as count")
                        ->pluck("count", "status_name"),
                    "total" => ModelsRequest::count(),
                ],
            ],
            "clients" => [
                "summary" => [
                    "split" => [
                        "zaufani" => User::where("trust", 1)->count(),
                        "krętacze" => User::where("trust", -1)->count(),
                        "patroni" => User::where("helped_showcasing", 2)->count(),
                        "bez zleceń" => User::withCount("questsDone")
                            ->having("quests_done_count", 0)
                            ->where("extra_exp", 0)
                            ->count(),
                        "kobiety" => User::all()
                            ->filter(fn($client) => $client->is_woman)
                            ->count(),
                    ],
                    "total" => User::all()->count(),
                ],
                "exp" => [
                    "split" => [
                        "weterani (".VETERAN_FROM()."+)" => client_exp_tally($client_exp_raw, VETERAN_FROM()),
                        "biegli (4-".(VETERAN_FROM()-1).")" => client_exp_tally($client_exp_raw, 4, VETERAN_FROM()-1),
                        "zainteresowani (2-3)" => client_exp_tally($client_exp_raw, 2, 3),
                        "nowicjusze (1)" => client_exp_tally($client_exp_raw, 1, 1),
                    ],
                    "total" => User::all()->count(),
                ],
                "new" => User::whereDate("created_at", ">=", Carbon::today()->subYear()->firstOfMonth())
                    ->whereDate("created_at", ">=", BEGINNING())
                    ->selectRaw("DATE_FORMAT(created_at, '%y-%m') as month,
                        count(*) as count")
                    ->groupBy("month")
                    ->orderBy("month")
                    ->pluck("count", "month"),
                "pickiness" => [
                    "high" => [
                        "rows" => User::all()
                            ->sortByDesc("pickiness")
                            ->take(10)
                            ->map(fn($item, $key) => [
                                "Nazwisko" => $item->client_name,
                                "Wybredność" => $item->pickiness * 100 . "%",
                            ])
                            ->values(),
                    ],
                    // "low" => [
                    //     "rows" => User::withCount("questsDone")
                    //         ->where("trust", ">", -1)
                    //         ->having("quests_done_count", ">", 0)
                    //         ->join("quests", "client_id", "users.id")
                    //         ->orderByDesc("quests.updated_at")
                    //         ->distinct("client_name")
                    //         ->get()
                    //         ->sortBy("pickiness")
                    //         ->take(5)
                    //         ->map(fn($item, $key) => [
                    //             "Nazwisko" => $item->client_name,
                    //             "Wybredność" => $item->pickiness * 100 . "%",
                    //         ])
                    //         ->values(0),
                    // ],
                ],
                "most_active" => [
                    "rows" => Top10::whereHasMorph(
                            "entity",
                            [User::class],
                            fn($q) => $q->where("type", "active")
                        )
                        ->get()
                        ->map(fn($item, $key) => [
                            "Nazwisko" => $item->entity->client_name,
                            "Liczba zleceń" => $item->entity->questsRecent()->count(),
                        ])
                        ->sortByDesc("Liczba zleceń")
                        ->values(),
                ],
            ],
            "finances" => [
                "income" => $recent_income->mapWithKeys(fn($vals, $month) => [$month => $vals[0]]),
                "prop" => $recent_income->mapWithKeys(fn($vals, $month) => [$month => $vals[1]]),
                "costs" => $recent_costs->mapWithKeys(fn($vals, $month) => [$month => $vals[0]]),
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
                    "main_raw" => DB::table(DB::raw("(".SongWorkTime::join("songs", "song_id", "songs.id", "left")
                            ->groupBy(["song_id", "genre_id"])
                            ->selectRaw("song_id, genre_id, sum(time_to_sec(time_spent)) as time_spent")
                            ->toSql().") as x"))
                        ->groupBy("genre_id")
                        ->join("genres", "genre_id", "genres.id")
                        ->selectRaw("name, avg(time_spent) as mean")
                        ->orderByDesc("mean")
                        ->pluck("mean", "name"),
                    "compared_to_raw" => DB::table(DB::raw("(".SongWorkTime::join("songs", "song_id", "songs.id", "left")
                            ->groupBy(["song_id", "genre_id"])
                            // ->whereDate("since", "<", Carbon::today()->subMonth()) //TODO naprawić
                            ->selectRaw("song_id, genre_id, sum(time_to_sec(time_spent)) as time_spent")
                            ->toSql().") as x"))
                        ->groupBy("genre_id")
                        ->join("genres", "genre_id", "genres.id")
                        ->selectRaw("name, avg(time_spent) as mean")
                        ->orderByDesc("mean")
                        ->pluck("mean", "name"),
                ],
            ],
        ];
        $stats = json_decode(json_encode($stats));

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
        $this_month = [
            "zarobiono" => StatusChange::whereDate("date", ">=", Carbon::today()->firstOfMonth())
                ->whereDate("date", "<=", Carbon::today()->lastOfMonth())
                ->where("new_status_id", 32)
                ->sum("comment"),
            "wydano" => Cost::whereDate("created_at", ">=", Carbon::today()->firstOfMonth())
                ->whereDate("created_at", "<=", Carbon::today()->lastOfMonth())
                ->where("cost_type_id", "!=", 3)
                ->sum("amount")
                -
                StatusChange::whereDate("date", ">=", Carbon::today()->firstOfMonth())
                ->whereDate("date", "<=", Carbon::today()->lastOfMonth())
                ->where("new_status_id", 34)
                ->sum("comment"),
            "wypłacono" => Cost::whereDate("created_at", ">=", Carbon::today()->firstOfMonth())
                ->whereDate("created_at", "<=", Carbon::today()->lastOfMonth())
                ->where("cost_type_id", 3)
                ->sum("amount"),
        ];

        $saturation = [
            "split" => $this->monthlyPaymentLimit()->getOriginalContent()["saturation"],
            "total" => INCOME_LIMIT(),
        ];
        $saturation = json_decode(json_encode($saturation));

        $unpaids = User::has("questsUnpaid")->orderBy("client_name")->get();

        $recent = StatusChange::where("new_status_id", 32)->orderByDesc("date")->limit(10)->get();
        foreach($recent as $i){
            $i->quest = Quest::find($i->re_quest_id);
            $i->new_status = Status::find($i->new_status_id);
        }

        $returns = Quest::where("status_id", 18)
            ->where("paid", true)
            ->get()
        ;

        return view(user_role().".finance", array_merge(
            ["title" => "Centrum Finansowe"],
            compact(
                "unpaids", "recent", "this_month", "saturation", "returns"
            ),
        ));
    }
    public function financePay(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest_ids = array_keys($rq->except("_token"));
        if(empty($quest_ids)) return back()->with("error", "Zaznacz zlecenia");

        $clients_quests = [];
        foreach($quest_ids as $id){
            $quest = Quest::find($id);
            $amount_to_pay = $quest->price - $quest->payments_sum;

            // opłać zlecenia
            BackController::newStatusLog(
                $id,
                32,
                $amount_to_pay,
                $quest->client_id,
                $quest->client->email,
            );

            // opłacanie faktury
            $invoice = InvoiceQuest::where("quest_id", $id)
            ->get()
            ->filter(fn($val) => !($val->isPaid))
            ->first();
            $invoice?->update(["paid" => $invoice->paid + $amount_to_pay]);
            // opłacanie faktury macierzystej
            $invoice = $invoice?->mainInvoice;
            $invoice?->update(["paid" => $invoice->paid + $amount_to_pay]);

            $quest->update(["paid" => (StatusChange::where(["new_status_id" => 32, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // zbierz zlecenia dla konkretnych adresatów
            $clients_quests[$quest->client_id][] = $quest;
        }

        // roześlij maile, jeśli można
        $clients_informed = [];
        foreach($clients_quests as $client_id => $quests){
            $client = User::find($client_id);
            if($client->email){
                Mail::to($client->email)->send(new MassPayment($quests));
                $clients_informed[$client_id] = 1;
            }else{
                $clients_informed[$client_id] = 0;
            }
        }
        $clients_informed_count = array_count_values($clients_informed);
        $clients_informed_output = (isset($clients_informed_count[1]) && $clients_informed_count[1] == count($clients_informed)) ? "wszyscy dostali maile"
            : ($clients_informed_count[0] == count($clients_informed) ? "nikt nie dostał maila" : ($clients_informed_count[1]."/".count($clients_informed)." klientów dostało maila"));

        return back()->with("success", "Zlecenia opłacone, $clients_informed_output");
    }

    public function financePayout(float $amount) {
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        Cost::create([
            "cost_type_id" => CostType::where("name", "like", "%wypłaty%")->first()->id,
            "desc" => "sam sobie",
            "amount" => $amount,
        ]);
        return back()->with("success", "Wykonano wypłatę kwoty ." . _c_(as_pln($amount)));
    }

    public function financeReturn(string $quest_id, bool $budget = false) {
        $quest = Quest::find($quest_id);
        $payments_sum = $quest->payments_sum;

        if (!$budget) {
            // wypłata
            BackController::newStatusLog(
                $quest_id,
                34,
                -$payments_sum,
                $quest->client_id
            );
        } else {
            // przesunięcie na budżet
            StatusChange::where("re_quest_id", $quest_id)
                ->where("new_status_id", 32)
                ->where("changed_by", $quest->client_id)
                ->update(["re_quest_id" => null]);

            $quest->client->budget += $payments_sum;
            $quest->client->save();
        }

        // oznacz jako nieopłacony
        $quest->update(["paid" => false]);

        $flash_content = "Zwrot wpisany" . (($budget) ? ", budżet zmieniony" : "");

        if($quest->client->email && !$budget){
            Mail::to($quest->client->email)->send(new PaymentReturned($quest->fresh()));
            StatusChange::where(["re_quest_id" => $quest_id, "new_status_id" => 34])->first()->update(["mail_sent" => true]);
            $flash_content .= ", mail wysłany";
        }
        if($quest->client->contact_preference != "email"){
            // StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => false]);
            $flash_content .= ", ale wyślij wiadomość";
        }

        return back()->with("success", $flash_content);
    }
    public function financeSummary(Request $rq){
        $gains = StatusChange::where("new_status_id", 32)
            ->whereDate("date", "like", (Carbon::today()->subMonths($rq->subMonths ?? 0)->format("Y-m"))."%")
            ->join("users", "users.id", "changed_by", "left")
            ->orderByDesc("date");
        $losses = Cost::whereDate("created_at", "like", (Carbon::today()->subMonths($rq->subMonths ?? 0)->format("Y-m"))."%")
            ->orderByDesc("created_at");

        $losses_payments = (clone $losses)->where("cost_type_id", 3)->sum("amount");
        $losses_other = (clone $losses)->where("cost_type_id", "<>", 3)->sum("amount")
            -
            StatusChange::where("new_status_id", 34)
            ->whereDate("date", "like", (Carbon::today()->subMonths($rq->subMonths ?? 0)->format("Y-m"))."%")
            ->sum("comment");

        $balance_now = StatusChange::whereIn("new_status_id", [32, 34])->sum("comment")
            - Cost::whereDate("created_at", ">=", BEGINNING())->sum("amount");

        $summary = [
            "Zarobiono" => $gains->sum("comment"),
            "Wydano" => $losses_other,
            "Wypłacono" => $losses_payments,
            "Saldo na dziś" => $balance_now,
            "Można wypłacić" => round(max($balance_now - setting("min_account_balance"), 0), 2),
        ];
        $gains = $gains->get();
        $losses = $losses->get();

        $losses = $losses->merge(
            StatusChange::where("new_status_id", 34)
                ->whereDate("date", "like", (Carbon::today()->subMonths($rq->subMonths ?? 0)->format("Y-m"))."%")
                ->join("users", "users.id", "changed_by", "left")
                ->get(["status_changes.*", "date as created_at"])
        )->sortByDesc("created_at");

        return view(user_role().".finance-summary", array_merge(
            ["title" => "Raport przepływów"],
            compact("gains", "losses", "summary")
        ));
    }

    public function invoices(Request $rq){
        $invoices = Invoice::orderByDesc("updated_at")->get();
        $client = ($rq->fillfor) ? User::findOrFail($rq->fillfor) : null;
        $quest_id = $rq->quest;

        return view(user_role().".invoices", array_merge(
            ["title" => "Lista faktur"],
            compact("invoices", "client", "quest_id")
        ));
    }

    public function invoice($id, Request $rq){
        $invoice = Invoice::with("quests")->findOrFail($id);

        return (substr($rq->path(), 0, 3) == "api")
            ? response()->json(["invoice" => $invoice])
            : view(user_role().".invoice", array_merge(
                ["title" => "Faktura nr ".$invoice->fullCode],
                compact("invoice"),
            ));
    }
    public function invoiceVisibility(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        Invoice::find($rq->id)->update(["visible" => $rq->visible]);

        return back()->with("success", $rq->visible ? "Faktura widoczna" : "Faktura schowana");
    }
    public function invoiceAdd(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $invoice_quests = [];
        $totals = ["amount" => 0, "paid" => 0];
        foreach(Quest::whereIn("id", explode(" ", $rq->quests))->get() as $quest){
            $invoice_quests[$quest->id] = [
                "amount" => $quest->price - $quest->allInvoices?->filter(fn($invoice) => $invoice->id != $rq->id)->sum("paid"),
                "paid" => ($quest->paid ? $quest->price - $quest->allInvoices?->filter(fn($invoice) => $invoice->id != $rq->id)->sum("paid") : 0),
                "primary" => count($quest->allInvoices?->filter(fn($invoice) => $invoice->id != $rq->id)) == 0,
            ];
            foreach(["amount", "paid"] as $i){
                $totals[$i] += $invoice_quests[$quest->id][$i];
            }
        }

        if ($rq->id) {
            // edycja
            $invoice = Invoice::find($rq->id);
            $invoice->update([
                "amount" => $totals["amount"],
                "paid" => $totals["paid"],
                "payer_name" => $rq->payer_name,
                "payer_title" => $rq->payer_title,
                "payer_address" => $rq->payer_address,
                "payer_nip" => $rq->payer_nip,
                "payer_regon" => $rq->payer_regon,
                "payer_email" => $rq->payer_email,
                "payer_phone" => $rq->payer_phone,
            ]);
            InvoiceQuest::where("invoice_id", $rq->id)->delete();
        } else {
            $invoice = Invoice::create([
                "visible" => false,
                "amount" => $totals["amount"],
                "paid" => $totals["paid"],
                "payer_name" => $rq->payer_name,
                "payer_title" => $rq->payer_title,
                "payer_address" => $rq->payer_address,
                "payer_nip" => $rq->payer_nip,
                "payer_regon" => $rq->payer_regon,
                "payer_email" => $rq->payer_email,
                "payer_phone" => $rq->payer_phone,
            ]);
        }

        foreach($invoice_quests as $quest_id => $values){
            InvoiceQuest::create([
                "invoice_id" => $invoice->id,
                "quest_id" => $quest_id,
                "primary" => $values["primary"],
                "amount" => $values["amount"],
                "paid" => $values["paid"],
            ]);
        }

        return redirect()->route("invoice", ["id" => $invoice->id])->with("success", ($rq->id) ? "Dokument poprawiony" : "Dokument utworzony");
    }

    public function costs(){
        $costs = Cost::orderByDesc("created_at")->paginate(25);
        $types = CostType::all()->pluck("name", "id");
        $types = collect(array_map(fn($el) => _ct_($el), $types->toArray()));
        $summary = [
            "Zwykłe" => Cost::where("cost_type_id", "<>", 3)->sum("amount"),
            "Wypłaty" => Cost::where("cost_type_id", 3)->sum("amount"),
            "Razem" => Cost::sum("amount"),
        ];

        return view(user_role().".costs", array_merge(
            ["title" => "Lista kosztów"],
            compact("costs", "types", "summary"),
        ));
    }
    public function modCost(Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $fields = [
            "cost_type_id" => $rq->cost_type_id,
            "desc" => $rq->desc,
            "amount" => $rq->amount,
            "created_at" => $rq->created_at,
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
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $fields = ["name" => $rq->name, "desc" => $rq->desc];
        if($rq->id) CostType::find($rq->id)->update($fields);
        else CostType::create($fields);

        return back()->with("success", "Gotowe");
    }

    public function fileSizeReport(){
        $safes = Storage::disk()->directories("safe");
        $sizes = []; $times = []; $songs = [];
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
            $songs[$safe] = Song::find(preg_replace('/.*\/(.{4}).*/', '$1', $safe));
        }
        arsort($sizes);

        return view(user_role().".file-size-report", array_merge(
            ["title" => "Raport zajętości serwera"],
            compact(
                "sizes", "times", "songs"
            ),
        ));
    }

    public function questsCalendar(){
        $free_days = CalendarFreeDay::orderBy("date")->whereDate("date", ">=", Carbon::today())->get();

        return view(user_role().".quests-calendar", array_merge(
            ["title" => "Grafik zleceń"],
            compact(
                "free_days"
            ),
        ));
    }
    public function qcModFreeDay(Request $rq){
        if($rq->mode == "add"){
            CalendarFreeDay::create(["date" => $rq->date]);
        }else{
            CalendarFreeDay::where("date", $rq->date)->delete();
        }
        return back()->with("success", "Dzień wolny ".($rq->mode == "add" ? "dodany" : "usunięty"));
    }

    public function monthlyPaymentLimit(Request $rq = null){
        //scheduled and received payments
        $saturation = [
            //this month
            StatusChange::whereDate("date", ">=", Carbon::today()->firstOfMonth())->where("new_status_id", 32)->sum("comment")
            + Quest::where("paid", 0)
                ->whereNotIn("status_id", [17, 18])
                ->whereHas("client", fn($q) => $q->where("trust", ">", -1)->where("trust", "<", 2))
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", "<", Carbon::today()->addMonth()->firstOfMonth())
                    ->orWhereNull("delayed_payment"))
                ->sum("price")
            + ModelsRequest::whereIn("status_id", [5])
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", "<", Carbon::today()->addMonth()->firstOfMonth())
                    ->orWhereNull("delayed_payment"))
                ->sum("price"),

            //next month (scheduled)
            Quest::where("paid", 0)
                ->whereNotIn("status_id", [17, 18])
                ->whereHas("client", fn($q) => $q->where("trust", ">", -1)->where("trust", "<", 2))
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", ">=", Carbon::today()->addMonth()->firstOfMonth())
                    ->whereDate("delayed_payment", "<=", Carbon::today()->addMonth()->lastOfMonth()))
                ->sum("price")
            + ModelsRequest::whereIn("status_id", [5])
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", ">=", Carbon::today()->addMonth()->firstOfMonth())
                    ->whereDate("delayed_payment", "<=", Carbon::today()->addMonth()->lastOfMonth()))
                ->sum("price"),

            //neeeeeeext month (scheduled)
            Quest::where("paid", 0)
                ->whereNotIn("status_id", [17, 18])
                ->whereHas("client", fn($q) => $q->where("trust", ">", -1)->where("trust", "<", 2))
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", ">=", Carbon::today()->addMonths(2)->firstOfMonth())
                    ->whereDate("delayed_payment", "<=", Carbon::today()->addMonths(2)->lastOfMonth()))
                ->sum("price")
            + ModelsRequest::whereIn("status_id", [5])
                ->where(fn($q) => $q
                    ->whereDate("delayed_payment", ">=", Carbon::today()->addMonths(2)->firstOfMonth())
                    ->whereDate("delayed_payment", "<=", Carbon::today()->addMonths(2)->lastOfMonth()))
                ->sum("price"),
        ];

        $when_to_ask = 0;
        $limit_corrected = INCOME_LIMIT() * 0.9;
        while($when_to_ask < 2){
            if($saturation[$when_to_ask] + ($rq->amount ?? 0) < $limit_corrected) break;
            else $when_to_ask++;
        }

        return response()->json(compact(
            "saturation",
            "when_to_ask",
            "limit_corrected",
        ));
    }

    public function taxes(Request $rq) {
        $fiscal_year = $rq->fiscalYear ?? date("Y") - 1;

        $money = [
            "Przychody" => StatusChange::where("new_status_id", 32)
                ->whereBetween("date", ["$fiscal_year-01-01", "$fiscal_year-12-31"])
                ->sum("comment"),
            "Koszty" => Cost::whereBetween("created_at", ["$fiscal_year-01-01", "$fiscal_year-12-31"])
                ->whereNotIn("cost_type_id", [3])
                ->sum("amount"),
        ];
        $money["Dochody"] = $money["Przychody"] - $money["Koszty"];
        $money["Podatek"] = tax_calc($money["Dochody"]);

        return view(user_role().".taxes", array_merge(
            ["title" => "Kwestie podatkowe"],
            compact(
                "fiscal_year",
                "money",
            ),
        ));
    }

    public function priceCalc(Request $request){
        return price_calc($request->labels, $request->client_id, $request->quoting);
    }

    #region gig-price
    public function gigPriceSuggest()
    {
        $defaults = [
            "Dojazd" => GigPriceDefault::relatedTo("drive")->get(),
            "Granie" => GigPriceDefault::relatedTo("show")->get(),
        ];
        $rates = GigPriceRate::orderBy("value")->get()
            ->mapWithKeys(fn ($r) => [$r->value => $r->label . " (" . _c_(as_pln($r->value)) . "/h)"]);
        $places = GigPricePlace::orderBy("name")->get()
            ->mapWithKeys(fn ($p) => [(strtolower($p->name) . "|" . $p->distance_km) => $p->name . " (" . $p->distance_km . " km)"]);

        return view(user_role().".gig-price.suggest", array_merge(
            ["title" => "Wycena grania"],
            compact("defaults", "rates", "places"),
        ));
    }

    public function gigPriceDefaults()
    {
        $defaults = GigPriceDefault::all();

        return view(user_role().".gig-price.defaults", array_merge(
            ["title" => "Ustawienia domyślne wyceny grania"],
            compact("defaults"),
        ));
    }

    public function gigPriceProcessDefaults(Request $rq)
    {
        foreach ($rq->except("_token") as $name => $value) {
            GigPriceDefault::find($name)->update(["value" => $value]);
        }

        return redirect()->route("gig-price-suggest")->with("success", "Ustawienia domyślne zmienione");
    }

    public function gigPriceRates()
    {

        $rates = GigPriceRate::orderBy("value")->get();

        return view(user_role().".gig-price.rates", array_merge(
            ["title" => "Stawki"],
            compact("rates"),
        ));
    }

    public function gigPriceRate(?GigPriceRate $rate = null)
    {
        return view(user_role().".gig-price.rate", array_merge(
            ["title" => "Stawka"],
            compact("rate"),
        ));
    }

    public function gigPriceProcessRate(Request $rq)
    {
        if ($rq->action == "save") {
            GigPriceRate::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
        } else if ($rq->action == "delete") {
            GigPriceRate::find($rq->id)->delete();
        }

        return redirect()->route("gig-price-rates")->with("success", "Stawka poprawiona");
    }

    public function gigPricePlaces()
    {
        $places = GigPricePlace::orderBy("name")->get();

        return view(user_role().".gig-price.places", array_merge(
            ["title" => "Miejsca"],
            compact("places"),
        ));
    }

    public function gigPricePlace(?GigPricePlace $place = null)
    {
        return view(user_role().".gig-price.place", array_merge(
            ["title" => "Miejsce"],
            compact("place"),
        ));
    }

    public function gigPriceProcessPlace(Request $rq)
    {
        if ($rq->action == "save") {
            GigPricePlace::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
        } else if ($rq->action == "delete") {
            GigPricePlace::find($rq->id)->delete();
        }

        return redirect()->route("gig-price-places")->with("success", "Miejsce poprawione");
    }
    #endregion
}
