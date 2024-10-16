<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageQuestMod;
use App\Mail\Clarification;
use App\Mail\Onboarding;
use App\Mail\PatronRejected;
use App\Mail\PaymentReceived;
use App\Mail\QuestRequoted;
use App\Mail\QuestUpdated;
use App\Mail\RequestQuoted;
use App\Models\Client;
use App\Models\ClientShowcase;
use App\Models\Invoice;
use App\Models\InvoiceQuest;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\SongWorkTime;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Str;
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

        $quest_types = QuestType::all()->pluck("type", "id")->toArray();
        $minimal_prices = array_combine($quest_types, QUEST_MINIMAL_PRICES());

        return view(user_role().".prices", array_merge(
            ["title" => "Cennik"],
            compact("prices", "discount", "minimal_prices")
        ));
    }

    public function requests(HttpRequest $rq){
        $client = Auth::user()->client;
        $client_id = $rq->client;
        $client_name = $rq->client_name;
        $status_id = $rq->status;

        $requests = Request::orderBy("updated_at", "desc");
        if(!is_archmage()){ $requests = $requests->where("client_id", $client->id); }
        if($client_id) $requests = $requests->where("client_id", $client_id);
        if($client_name) $requests = $requests->where("client_name", "regexp", $client_name);
        if($status_id) $requests = $requests->where("status_id", $status_id);
        $requests = $requests->paginate(25);

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }
    public function request($id){
        $request = Request::find($id);
        if(!$request) abort(404, "Nie ma takiego zapytania");
        $pad_size = 30; // used by dropdowns for mobile preview fix

        if(is_archmage()){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = _ct_("$client[client_name] «$client[id]»");
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = Str::limit($song["title"] ?? "bez tytułu ($song[artist])", $pad_size)." «$song[id]»";
            }
        }else{
            if($request->client_id != Auth::id()){
                if(Auth::id()) abort(403, "To nie jest Twoje zapytanie");
                else return redirect()->route("login")->with("error", "Zaloguj się, jeśli to Twoje zapytanie");
            };
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")
            ->where("quest_type_id", $request->quest_type_id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        $warnings = is_archmage() ? [
            'song' => [
                'Klient ma domyślne życzenia' => $request->client?->default_wishes,
                'Klient ma deadline' => $request->hard_deadline,
            ],
        ] : [
            "quote" => [
                'Zwróć uwagę, kiedy masz zapłacić' => $request->delayed_payment,
                'Wycena nadal w przygotowaniu' => !$request->price,
                'Wycena może być nieaktualna' => $request->price && $request->status_id == 1,
            ],
        ];

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres", "warnings")));
    }
    public function addRequest(HttpRequest $rq){
        $pad_size = 24; // used by dropdowns for mobile preview fix

        if (is_archmage()) {
            // arcymag od razu tworzy pusty request i edytuje go później
            $client_data = [];
            if ($rq->has("client")) $client_data["client_id"] = $rq->client;
            if ($rq->has("client_new")) {
                $client_data = array_filter(
                    array_combine(
                        [
                            "client_name",
                            "email",
                            "phone",
                            "other_medium",
                            "contact_preference",
                        ],
                        explode("*", $rq->client_new)
                    ),
                    fn($el) => $el != ""
                );
            }

            $request = Request::create(array_merge(
                ["status_id" => 1],
                $client_data
            ));
            $this->statusHistory($request->id, 1, "~ spoza strony");
            return redirect()->route("request", ["id" => $request->id])->with("success", "Szablon zapytania gotowy");
        }

        $clients = [];
        $songs = [];

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs", "genres")));
    }

    public function addRequestBack(HttpRequest $rq){
        if(isset($rq->m_test) && $rq->m_test != 20) return redirect()->route("home")->with("error", "Cztery razy pięć nie równa się $rq->m_test");
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());

        $flash_content = "Zapytania dodane";
        $loop_length = is_array($rq->quest_type) ? count($rq->quest_type) : 1;
        $requests_created = [];

        for($i = 0; $i < $loop_length; $i++){
            if(is_archmage()){ // arcymag
                //non-bulk
                $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
                $client = ($rq->client_id) ? Client::find($rq->client_id) : null;

                $request = Request::create([
                    "made_by_me" => true,

                    "client_id" => $rq->client_id,
                    "client_name" => $rq->client_name,
                    "email" => $rq->email,
                    "phone" => $rq->phone,
                    "other_medium" => $rq->other_medium,
                    "contact_preference" => $rq->contact_preference ?? "email",

                    "song_id" => $rq->song_id,
                    "quest_type_id" => $rq->quest_type,
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => yt_cleanup($rq->link),
                    "genre_id" => $rq->genre_id,
                    "wishes" => $rq->wishes,
                    "wishes_quest" => $rq->wishes_quest,

                    "price_code" => price_calc($rq->price_code, $rq->client_id, true)["labels"],
                    "price" => price_calc($rq->price_code, $rq->client_id, true)["price"],
                    "deadline" => $rq->deadline,
                    "hard_deadline" => $rq->hard_deadline,
                    "delayed_payment" => $rq->delayed_payment,
                    "status_id" => $rq->new_status,
                ]);

                // nadpisanie zmienionych gotowców
                if($rq->song_id){
                    Song::find($rq->song_id)->update([
                        "title" => $rq->title,
                        "artist" => $rq->artist,
                        "link" => yt_cleanup($rq->link),
                        "genre_id" => $rq->genre_id,
                        "price_code" => preg_replace("/[=\-oyzqr\d]/", "", $rq->price_code),
                        "notes" => $rq->wishes,
                    ]);
                }
                if($rq->client_id){
                    Client::find($rq->client_id)->update([
                        "client_name" => $rq->client_name,
                        "email" => $rq->email,
                        "phone" => $rq->phone,
                        "other_medium" => $rq->other_medium,
                        "contact_preference" => $rq->contact_preference,
                    ]);
                }

                if($rq->new_status == 5) $this->statusHistory($request->id, 1, null);
                $this->statusHistory($request->id, $rq->new_status, $rq->comment);

                //mailing
                $mailing = null;
                if(
                    $request->email &&
                    $rq->new_status == 5
                ){
                    Mail::to($request->email)->send(new RequestQuoted($request->fresh()));
                    $mailing = true;
                    $flash_content .= ", mail wysłany";
                }
                if($request->contact_preference != "email"){
                    $mailing ??= false;
                    $flash_content .= ", ale wyślij wiadomość";
                }
                if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);
            }else{ // klient
                //bulk

                // ignore empty requests
                if (empty($rq->title[$i]) && empty($rq->artist[$i]) && empty($rq->link[$i])) {
                    continue;
                }

                $request = Request::create([
                    "made_by_me" => false,

                    "client_id" => (Auth::check()) ? Auth::id() : null,
                    "client_name" => (Auth::check()) ? Auth::user()->client->client_name : $rq->client_name,
                    "email" => (Auth::check()) ? Auth::user()->client->email : $rq->email,
                    "phone" => (Auth::check()) ? Auth::user()->client->phone : $rq->phone,
                    "other_medium" => (Auth::check()) ? Auth::user()->client->other_medium : $rq->other_medium,
                    "contact_preference" => (Auth::check()) ? Auth::user()->client->contact_preference : $rq->contact_preference,

                    "quest_type_id" => $rq->quest_type[$i],
                    "title" => $rq->title[$i],
                    "artist" => $rq->artist[$i],
                    "link" => yt_cleanup($rq->link[$i]),
                    "wishes" => $rq->wishes[$i],

                    "hard_deadline" => $rq->hard_deadline[$i],
                    "status_id" => $rq->new_status,
                ]);

                //mailing do mnie
                $mailing = null;
                Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
                $mailing = true;

                $this->statusHistory($request->id, $rq->new_status, $rq->wishes[$i], (Auth::check()) ? Auth::id() : null, $mailing);
            }
            $requests_created[] = $request;
        }

        if(Auth::check()) return redirect()->route("dashboard")->with("success", $flash_content);
        return view("client.request-confirm", array_merge(
            ["title" => "Zapytanie dodane"],
            compact("requests_created")
        ))->with("success", $flash_content);
    }

    public function modRequestBack(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $intent = $rq->intent;
        $request = Request::find($rq->id);

        $is_same_status = $request->status_id == $rq->new_status;

        if($intent == "change"){
            $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
            $client = ($rq->client_id) ? Client::find($rq->client_id) : null;

            // price required when sending quote
            if(
                $rq->new_status == 5 &&
                (!$rq->price_code)
            ){
                return back()->with("error", "Uzupełnij wycenę");
            }

            $request->update([
                "client_id" => $rq->client_id,
                "client_name" => $rq->client_name,
                "email" => $rq->email,
                "phone" => $rq->phone,
                "other_medium" => $rq->other_medium,
                "contact_preference" => $rq->contact_preference ?? "email",

                "song_id" => $rq->song_id,
                "quest_type_id" => $rq->quest_type,
                "title" => $rq->title,
                "artist" => $rq->artist,
                "link" => yt_cleanup($rq->link),
                "genre_id" => $rq->genre_id,
                "wishes" => $rq->wishes,
                "wishes_quest" => $rq->wishes_quest,

                "price_code" => price_calc($rq->price_code, $rq->client_id, true)["labels"],
                "price" => price_calc($rq->price_code, $rq->client_id, true)["price"],
                "deadline" => $rq->deadline,
                "hard_deadline" => $rq->hard_deadline,
                "delayed_payment" => $rq->delayed_payment,
                "status_id" => $rq->new_status,
            ]);

            // nadpisanie zmienionych gotowców
            if($song){
                $song->update([
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => yt_cleanup($rq->link),
                    "genre_id" => $rq->genre_id,
                    "price_code" => preg_replace("/[=\-oyzqr\d]/", "", $rq->price_code),
                    "notes" => $rq->wishes,
                ]);
            }
            if($client){
                $client->update([
                    "client_name" => $rq->client_name,
                    "email" => $rq->email,
                    "phone" => $rq->phone,
                    "other_medium" => $rq->other_medium,
                    "contact_preference" => $rq->contact_preference,
                ]);
            }

            $changes = ($request->price || $request->deadline) ? [
                "cena" => $request->price,
                "termin wykonania" => $request->deadline?->format("d.m.Y"),
            ] : null;
        }else if($intent == "review"){
            //review jako klient
            $request->status_id = $rq->new_status;

            $changes = [];

            if($rq->new_status == 1){
                // $request->price = null;
                // $request->price_code = null;
                $request->deadline = null;
                $request->hard_deadline = null;
                $request->delayed_payment = null;
            }elseif($rq->new_status == 6 && $rq->optbc){
                $changes = ["zmiana ($rq->optbc)" => $request->{$rq->optbc} . " → " . $rq->{"opinion_".$rq->optbc}];
                $request->{$rq->optbc} = $rq->{"opinion_".$rq->optbc};
            }
            $request->save();
        }

        $changed_by = (is_archmage() && in_array($rq->new_status, [1, 6, 8, 9, 96])) ? $request->client_id : null;
        if($is_same_status){
            $request->history->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            $this->statusHistory($request->id, $request->status_id, $rq->comment, $changed_by, null, $changes);
        }

        // sending mail
        $flash_content = "Zapytanie gotowe";
        $mailing = null;
        if(in_array($request->status_id, [5, 95])){ // mail do klienta
            if($request->email){
                switch($request->status_id){
                    case 5: Mail::to($request->email)->send(new RequestQuoted($request->fresh())); break;
                    case 95: Mail::to($request->email)->send(new Clarification($request->fresh())); break;
                }
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($request->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if($request->status_id != 4){ // mail do mnie
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);

        return redirect()->route("request", ["id" => $request->id])->with("success", $flash_content);
    }

    public function requestFinal($id, $status, $with_priority = false){
        $request = Request::find($id);
        if(!$request) abort(404, "Nie ma takiego zapytania");

        if(in_array($request->status_id, [4,7,8,9]))
            return redirect()->route("request", ["id" => $id])->with("error", "Zapytanie już zamknięte");

        $request->status_id = $status;
        $price = price_calc($request->price_code.(($with_priority) ? "z" : ""), $request->client_id, true);

        $is_new_client = 0;

        // adding new quest
        if($status == 9){
            //add new song if not exists
            if(!$request->song_id){
                $song = new Song;
                $song->id = next_song_id($request->quest_type_id);
                $song->title = $request->title;
                $song->artist = $request->artist;
                $song->link = yt_cleanup($request->link);
                $song->genre_id = $request->genre_id;
                $song->price_code = preg_replace("/[=\-oyzqr\d]/", "", $request->price_code);
                $song->notes = $request->wishes;
                $song->save();

                $request->song_id = $song->id;
            }
            //add new client if not exists
            if(!$request->client_id){
                $is_new_client = 1;
                $user = new User;
                $user->password = generate_password();
                $user->save();

                $client = new Client;
                $client->id = $user->id;
                $client->client_name = $request->client_name;
                $client->email = $request->email;
                $client->phone = $request->phone;
                $client->other_medium = $request->other_medium;
                $client->contact_preference = $request->contact_preference;
                $client->save();

                $request->client_id = $user->id;

                //bind remaining anonymous quests from the same guy
                Request::whereIn("status_id", [1, 5])
                    ->where("client_name", $request->client_name)
                    ->where("email", $request->email)
                    ->where("phone", $request->phone)
                    ->where("other_medium", $request->other_medium)
                    ->where("contact_preference", $request->contact_preference)
                    ->update(["client_id" => $user->id]);
            }else{
                $client = Client::find($request->client_id);
            }

            $quest = new Quest;
            $quest->id = next_quest_id($request->quest_type_id);
            $quest->song_id = $request->song_id ?? $song->id;
            $quest->client_id = $request->client_id ?? $user->id;
            $quest->status_id = 11;
            $quest->price_code_override = $price["labels"];
            $quest->price = $price["price"];
            $quest->deadline = ($with_priority) ? get_next_working_day() : $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->delayed_payment = $request->delayed_payment;
            $quest->wishes = $request->wishes_quest;
            $quest->save();

            // $invoice = Invoice::create([
            //     "quest_id" => $quest->id,
            //     "amount" => $quest->price
            // ]);

            if($client->budget){
                $sub_amount = min([$request->price, $client->budget]);
                $client->budget -= $sub_amount;
                $this->statusHistory(null, 32, -$sub_amount, $client->id);
                if($sub_amount == $request->price){
                    $quest->paid = true;
                    $quest->save();
                }
                $client->save();
                $this->statusHistory($quest->id, 32, $sub_amount, $client->id);
                // $invoice->update(["paid" => $sub_amount]);
            }

            $request->quest_id = $quest->id;
            $request->price_code = $price["labels"];
            $request->price = $price["price"];
        }

        $request->save();

        //mail do mnie, bo zmiany w zapytaniu
        $mailing = null;
        Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request->fresh()));
        $mailing = true;

        $this->statusHistory($id, $status, null, (is_archmage()) ? $request->client_id : null, $mailing);

        if($status == 9){
            //added quest
            $this->statusHistory($request->quest_id, 11, null, $request->client_id);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
            //send onboarding if new client
            if($request->client->email && $is_new_client){
                Mail::to($request->client->email)->send(new Onboarding($request->client));
            }
        }

        if(is_archmage()) return redirect()->route("request", ["id" => $request->id]);
        else return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
    }

    public function requestFinalized($id, $status, $is_new_client){
        $request = Request::findOrFail($id);

        return view("request-finalized", array_merge(
            ["title" => "Zapytanie zamknięte"],
            compact("id", "status", "is_new_client", "request")
        ));
    }

    public function statusHistory($re_quest_id, $new_status_id, $comment, $changed_by = null, $mailing = null, $changes = null){
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
    public function questReject(HttpRequest $rq){
        //adding comment
        $history = StatusChange::where("re_quest_id", $rq->id)
            ->where("new_status_id", $rq->status)
            ->first();

        $history->values = json_encode(["powód" => $rq->comment]);
        $history->save();

        $where_to = (!Auth::check()) ? "home" : "dashboard";
        return redirect()->route($where_to)->with("success", "Komentarz dodany");
    }

    public function quests(HttpRequest $rq){
        $client_id = $rq->client;
        $status_id = $rq->status;
        $paid = $rq->paid;

        $client = Client::find($client_id) ?? Auth::user()->client;
        if($client_id && $client_id != Auth::id() && !is_archmage()) abort(403, "Widok niedostępny");

        $quests = Quest::orderBy("quests.created_at", "desc");
        if($client){ $quests = $quests->where("client_id", $client->id); }
        if($status_id) $quests = $quests->where("status_id", $status_id);
        if($paid) $quests = $quests->where("paid", $paid);
        $quests = $quests->paginate(25);

        return view(user_role().".quests", [
            "title" => $client_id ? "$client->client_name – zlecenia" : "Lista zleceń",
            "quests" => $quests
        ]);
    }

    public function quest($id){
        $quest = Quest::find($id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");

        $prices = DB::table("prices")
            ->where("quest_type_id", $quest->song->type->id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        if($quest->client_id != Auth::id() && !is_archmage()) abort(403, "To nie jest Twoje zlecenie");

        $files_raw = collect(Storage::files('safe/'.$quest->song_id))
            ->sortByDesc(function($file){return Storage::lastModified($file);});
        $files = []; $last_mod = []; $desc = [];

        if(!empty($files_raw)){
            foreach($files_raw as $file){
                $name = [];
                $name_raw = pathinfo($file, PATHINFO_FILENAME);
                preg_match("/_(.*)$/", $name_raw, $matches);
                if(!empty($matches)){
                    $name[2] = $matches[1];
                    $name_raw = str_replace("_".$name[2], "", $name_raw);
                }
                preg_match("/=(.*)$/", $name_raw, $matches);
                if(!empty($matches)){
                    $name[1] = $matches[1];
                    $name_raw = str_replace("=".$name[1], "", $name_raw);
                }
                $name[0] = $name_raw;

                if(!isset($name[1])) $name[1] = "podstawowy";
                if(!isset($name[2])) $name[2] = "wersja główna";
                $files[$name[0]][$name[1]][$name[2]][] = $file;
                $last_mod[$name[1]][$name[2]] = Carbon::parse(Storage::lastModified($file));
                if(pathinfo($file, PATHINFO_EXTENSION) == "md") $desc[$name[0]][$name[1]][$name[2]] = $file;
            }
        }

        $warnings = is_archmage() ? [
            "files" => [
                'Pliki nieoznaczone jako komplet' => $quest->status_id != 11 && !$quest->files_ready,
            ],
            "quote" => [
                'Ostatnia zmiana padła '.$quest->history->get(1)?->date->diffForHumans() => in_array($quest->status_id, [16, 26]) && $quest->history->get(1)?->date->diffInDays() >= 30,
                'Opóźnienie wpłaty' => $quest->delayed_payment_in_effect,
            ],
        ] : [
            "quote" => [
                'Zwróć uwagę, kiedy masz zapłacić' => !!$quest->delayed_payment_in_effect,
                'Zlecenie nieopłacone' => $quest->client->trust == -1
                    || $quest->status_id == 19 && !$quest->paid
                    || $quest->payments_sum > 0 && $quest->payments_sum < $quest->price,
            ],
        ];

        return view(
            user_role().".quest",
            array_merge(
                ["title" => "Zlecenie"],
                compact("quest", "prices", "files", "last_mod", "desc", "warnings"),
                (isset($stats_statuses) ? compact("stats_statuses") : []),
            )
        );
    }

    public function modQuestBack(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->quest_id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        if(SongWorkTime::where(["song_id" => $quest->song_id, "now_working" => 1])->first()){
            return back()->with("error", "Zatrzymaj zegar");
        }

        // wpisywanie wpłaty za zlecenie
        if($rq->status_id == 32){
            if(empty($rq->comment)) return redirect()->route("quest", ["id" => $rq->quest_id])->with("error", "Nie podałeś ceny");

            // opłacenie zlecenia (sprawdzenie nadwyżki)
            $amount_to_pay = $quest->price - $quest->payments_sum;
            $amount_for_budget = $rq->comment - $amount_to_pay;

            $this->statusHistory($rq->quest_id, $rq->status_id, min($rq->comment, $amount_to_pay), $quest->client_id);
            if($amount_for_budget > 0){
                $this->statusHistory(null, $rq->status_id, $amount_for_budget, $quest->client_id);

                // budżet
                $quest->client->budget += $amount_for_budget;
                $quest->client->save();
            }

            // opłacanie faktury
            $invoice = InvoiceQuest::where("quest_id", $rq->quest_id)
                ->get()
                ->filter(fn($val) => !($val->isPaid))
                ->first();
            $invoice?->update(["paid" => $invoice->paid + $rq->comment]);
            // opłacanie faktury macierzystej
            $invoice = $invoice?->mainInvoice;
            $invoice?->update(["paid" => $invoice->paid + $rq->comment]);

            $quest->update(["paid" => (StatusChange::where(["new_status_id" => $rq->status_id, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // sending mail
            $flash_content = "Cena wpisana";
            if($quest->paid){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new PaymentReceived($quest->fresh()));
                    StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => true]);
                    $flash_content .= ", mail wysłany";
                }
                if($quest->client->contact_preference != "email"){
                    // StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => false]);
                    $flash_content .= ", ale wyślij wiadomość";
                }
            }

            // wycofanie statusu krętacza
            if ($quest->client->trust == -1 && $quest->client->quests_unpaid->count() == 0) {
                $quest->client->update(["trust" => 0]);
                $flash_content .= "; już nie jest krętaczem";
            }

            return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
        }

        $is_same_status = $quest->status_id == $rq->status_id;
        $quest->status_id = $rq->status_id;

        // files ready checkpoint
        if(in_array($rq->status_id, [16, 26])){
            $quest->files_ready = false;
        }

        // handle rejecting new quote
        $last_status = $quest->history->first();
        if ($last_status->new_status_id == 31 && $rq->status_id == 19) {
            $fields = [
                "cena" => "price",
                "kod wyceny" => "price_code_override",
                "do kiedy (włącznie) oddam pliki" => "deadline",
                "opóźnienie wpłaty" => "delayed_payment",
            ];

            $changes = json_decode($last_status->values);

            foreach ($changes as $label => $change) {
                if ($label == "zmiana z uwagi na") continue;

                [$prev, $next] = explode(" → ", $change);
                $quest->{$fields[$label]} = empty($prev) ? null : $prev;
            }
            $quest->paid = true;
        }

        $quest->save();

        if($is_same_status){
            $quest->history->first()->update(["comment" => $rq->comment, "date" => now()]);
        }else{
            $this->statusHistory(
                $rq->quest_id,
                $rq->status_id,
                $rq->comment,
                (is_archmage() && in_array($rq->status_id, [16, 18, 19, 21, 26, 96])) ? $quest->client_id : null
            );
        }

        // sending mail
        $flash_content = "Faza zmieniona";
        $mailing = null;
        if(
            in_array($quest->status_id, [15, 95])
            || $quest->status_id == 11 && is_archmage()
        ){ // mail do klienta
            if($quest->client->email){
                Mail::to($quest->client->email)->send(
                    $quest->status_id == 95
                    ? new Clarification($quest->fresh())
                    : new QuestUpdated($quest->fresh())
                );
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }
            if($quest->client->contact_preference != "email"){
                $mailing ??= false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if(!is_archmage()){ // mail do mnie
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($quest->fresh()));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $quest->history->first()->update(["mail_sent" => $mailing]);


        return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
    }
    public function questSongUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $song = Song::find($rq->id);
        if(!$song) abort(404, "Nie ma takiego utworu");
        $song->update([
            "title" => $rq->title,
            "artist" => $rq->artist,
            "link" => yt_cleanup($rq->link),
            "notes" => $rq->wishes,
        ]);
        return back()->with("success", "Utwór zmodyfikowany");
    }
    public function questWishesUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        $quest->update([
            "wishes" => $rq->wishes_quest,
        ]);
        return back()->with("success", "Zlecenie zmodyfikowane");
    }
    public function questQuoteUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        $price_before = $quest->price;
        $price_code_before = $quest->price_code_override;
        $deadline_before = $quest->deadline;
        $delayed_payment_before = $quest->delayed_payment;
        $quest->update([
            "price_code_override" => $rq->price_code_override,
            "price" => price_calc($rq->price_code_override, $quest->client_id)["price"],
            "paid" => ($quest->payments_sum >= price_calc($rq->price_code_override, $quest->client_id)["price"]),
            "deadline" => $rq->deadline,
            "delayed_payment" => $rq->delayed_payment,
        ]);
        $difference = $quest->price - $price_before;
        if($quest->client->budget){
            $sub_amount = min([$difference, $quest->client->budget]);
            $quest->client->budget -= $sub_amount;
            $this->statusHistory(null, 32, -$sub_amount, $quest->client->id);
            if($sub_amount == $difference){
                $quest->paid = true;
                $quest->save();
            }
            $quest->client->save();
            $this->statusHistory($quest->id, 32, $sub_amount, $quest->client->id);
        }

        if($price_before != $quest->price){
            InvoiceQuest::where("quest_id", $quest->id)->update(["amount" => $quest->price]);
            $invoice_amount = Invoice::whereHas("quests", fn($q) => $q->where("quest_id", $quest->id))->first()?->quests->sum("price");
            Invoice::whereHas("quests", fn($q) => $q->where("quest_id", $quest->id))->update(["amount" => $invoice_amount]);
        }

        // sending mail
        $mailing = null;
        if($quest->client->email){
            Mail::to($quest->client->email)->send(new QuestRequoted($quest->fresh(), $rq->reason, $difference));
            $mailing = true;
        }
        if($quest->client->contact_preference != "email"){
            $mailing ??= false;
        }

        // zbierz zmiany
        $changes = [];
        foreach([
            "cena" => [$price_before, $quest->price],
            "do kiedy (włącznie) oddam pliki" => [$deadline_before->format("Y-m-d"), $quest->deadline->format("Y-m-d")],
            "opóźnienie wpłaty" => [$delayed_payment_before?->format("Y-m-d"), $quest->delayed_payment?->format("Y-m-d")],
            "kod wyceny" => [$price_code_before, $quest->price_code_override],
        ] as $attr => $value){
            if ($value[0] != $value[1]) $changes[$attr] = $value[0] . " → " . $value[1];
        }
        $changes["zmiana z uwagi na"] = $rq->reason;

        // change quest status
        $quest->update([
            "status_id" => 31,
            "files_ready" => false,
        ]);

        app("App\Http\Controllers\BackController")->statusHistory(
            $rq->id,
            31,
            null,
            null,
            $mailing,
            $changes
        );
        return back()->with("success", "Wycena zapytania zmodyfikowana");
    }

    public function questFilesReadyUpdate(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->quest_id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");
        $quest->update([
            "files_ready" => $rq->ready,
        ]);
        return back()->with("success", "Zawartość sejfu zatwierdzona");
    }

    public function questFilesExternalUpdate(HttpRequest $rq) {
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        $quest = Quest::find($rq->quest_id);
        if(!$quest) abort(404, "Nie ma takiego zlecenia");

        $quest->update([
            "has_files_on_external_drive" => $rq->external,
        ]);
        return back()->with("success", "Zmieniono status chmury");
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

    public function songs(HttpRequest $rq){
        $search = strtolower($rq->search ?? "");
        $songs = Song::orderBy("title")
            ->whereRaw("LOWER(title) like '%$search%'")
            ->orWhereRaw("LOWER(artist) like '%$search%'")
            ->orWhereRaw("LOWER(id) like '%$search%'")
            ->paginate();

        $song_work_times = [];
        $price_codes = [];
        foreach($songs as $song){
            $price_codes[$song->id] = [];
            for($i = 0; $i < strlen($song->price_code); $i++){
                $letter = $song->price_code[$i];
                if(preg_match('/[a-z]/', $song->price_code[$i])){
                    $price_codes[$song->id][] = DB::table("prices")->where("indicator", $letter)->value("service");
                }
            }
            $price_codes[$song->id] = implode("<br>", $price_codes[$song->id]);

            $song_work_times[$song->id] = [
                "total" => gmdate("H:i:s", DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->sum(DB::raw("TIME_TO_SEC(time_spent)"))),
                "parts" => DB::table("song_work_times")
                    ->where("song_id", $song->id)
                    ->get()
                    ->toArray()
            ];
            $song_work_times[$song->id]["parts"] = implode("<br>", array_map(function($x){
                return Status::find($x->status_id)->status_name . " → " . $x->time_spent;
            }, $song_work_times[$song->id]["parts"]));
        }

        return view(user_role().".songs", array_merge(
            ["title" => "Lista utworów"],
            compact("songs", "song_work_times", "price_codes", "search")
        ));
    }

    public function showcases(){
        $showcases = Showcase::orderBy("updated_at", "desc")->paginate(5);
        $client_showcases = ClientShowcase::orderBy("updated_at", "desc")->paginate(5);

        $songs_raw = Song::whereDoesntHave('showcase')
            ->whereHas('quests', function($q){
                $q->where('status_id', 19);
            })
            ->orderByDesc("created_at")
            ;

        $potential_showcases = clone $songs_raw;
        $potential_showcases = $potential_showcases->whereDate('created_at', '>', Carbon::today()->subMonth()->format("Y-m-d H:i:s"))->get();

        $songs_raw = $songs_raw->get()->toArray();
        foreach($songs_raw as $song){
            $songs[$song["id"]] = "$song[title] ($song[artist]) [$song[id]]";
        }

        $all_songs = Song::orderBy("title")
            ->orderBy("artist")
            ->orderBy("id")
            ->get()
            ->mapWithKeys(fn($s) => [$s["id"] => "$s[title] ($s[artist]) [$s[id]]"]);

        return view(user_role().".showcases", array_merge(
            ["title" => "Lista reklam"],
            compact("showcases", "client_showcases", "songs", "all_songs", "potential_showcases")
        ));
    }

    public function addShowcase(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        Showcase::create([
            "song_id" => $rq->song_id,
            "link_fb" => (filter_var($rq->link_fb, FILTER_VALIDATE_URL)) ?
                "<a target='_blank' href='$rq->link_fb'>$rq->link_fb</a>" : $rq->link_fb,
            "link_ig" => $rq->link_ig,
        ]);

        return back()->with("success", "Dodano pozycję");
    }

    public function addShowcaseFromClient(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        ClientShowcase::create([
            "song_id" => $rq->song_id,
            "embed" => $rq->embed,
        ]);

        return back()->with("success", "Dodano pozycję");
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
}
