<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shipyard\AuthController;
use App\Mail\ArchmageQuestMod;
use App\Mail\Clarification;
use App\Mail\NewRequest\Dj;
use App\Mail\NewRequest\Organista;
use App\Mail\NewRequest\Podklady;
use App\Mail\Onboarding;
use App\Mail\RequestQuoted;
use App\Models\IncomeType;
use App\Models\MoneyTransaction;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request;
use App\Models\Song;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function list(HttpRequest $rq){
        $status_id = $rq->status;
        $client_name = $rq->client_name;

        $client = User::find(is_archmage() ? $rq->client : Auth::id());

        $requests = Request::orderBy("updated_at", "desc");
        if($client){ $requests = $requests->where("client_id", $client->id); }
        if (is_archmage()) {
            if($client_name) $requests = $requests->where("client_name", "regexp", $client_name);
            if($status_id) $requests = $requests->where("status_id", $status_id);
        }

        $requests = $requests->paginate(25);

        return view("pages.".user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }

    public function show($id){
        $request = Request::findOrFail($id);
        $pad_size = 30; // used by dropdowns for mobile preview fix

        if(is_archmage()){
            $clients = User::all()
                ->mapWithKeys(fn ($c) => [$c->id => _ct_($c)])
                ->toArray();
            $songs = Song::all()
                ->mapWithKeys(fn ($s) => [$s->id => Str::limit($s->title ?? "bez tytułu ($s->artist)", $pad_size)." «$s[id]»"])
                ->toArray();
        }else{
            if($request->client_id != Auth::id()){
                if(Auth::id()) abort(403, "To nie jest Twoje zapytanie");
                else return redirect()->route("login")->with("toast", ["error", "Zaloguj się, jeśli to Twoje zapytanie"]);
            };
            $clients = [];
            $songs = [];
        }

        $questTypes = QuestType::all()
            ->mapWithKeys(fn ($qt) => [$qt->id => $qt->type])
            ->toArray();

        $prices = DB::table("prices")
            ->where("quest_type_id", $request->quest_type_id)->orWhereNull("quest_type_id")
            ->orderBy("quest_type_id")->orderBy("indicator")
            ->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        // detecting youtube link and finding similar songs
        $matcher =  Str::of($request->link)
            ->matchAll("/v?[=\/]([A-Za-z0-9\-\_]{11})/")
            ->join("|");
        $similar_songs = ($request->link && $request->status_id == 1 && $matcher)
            ? Song::where("link", "regexp", $matcher)->get()
            : collect();

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

        return view("pages.".user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres", "warnings", "similar_songs")));
    }

    #region new requests
    public function add(HttpRequest $rq){
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
            BackController::newStatusLog($request->id, 1, "~ spoza strony");
            return redirect()->route("request", ["id" => $request->id])->with("toast", ["success", "Szablon zapytania gotowy"]);
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

        return view("pages.".user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs", "genres")));
    }

    private function checkFormTest(HttpRequest $rq)
    {
        $value = $rq->input("test");
        $test_ok = strtolower($value) === "dwadzieścia"
            || strtolower($value) === "dwadziescia"
            || $value == 20;

        if (!$test_ok) {
            return back()->with("toast", ["error", "Nie możemy potwierdzić, czy jesteś robotem. Spróbuj ponownie."]);
        }
    }

    public function newRequestPodklady(HttpRequest $rq)
    {
        $this->checkFormTest($rq);

        return $this->processAdd($rq);
    }

    public function newRequestOrganista(HttpRequest $rq)
    {
        $this->checkFormTest($rq);

        $data = $rq->all();

        Mail::to(env("MAIL_MAIN_ADDRESS"))
            ->send(new Organista($data));

        return back()->with("toast", ["success", "Zapytanie wysłane. Wkrótce na nie odpowiem"]);
    }

    public function newRequestDj(HttpRequest $rq)
    {
        $this->checkFormTest($rq);

        $data = $rq->all();

        Mail::to(env("MAIL_MAIN_ADDRESS"))
            ->send(new Dj($data));

        return back()->with("toast", ["success", "Zapytanie wysłane. Wkrótce na nie odpowiem"]);
    }
    #endregion

    public function finalized($id, $status, $is_new_client){
        $request = Request::findOrFail($id);

        return view("request-finalized", array_merge(
            ["title" => "Zapytanie zamknięte"],
            compact("id", "status", "is_new_client", "request")
        ));
    }

    ////////////////////////////////////////////////////

    public function processAdd(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);

        $flash_content = "Zapytania dodane";
        $loop_length = is_array($rq->quest_type_id) ? count($rq->quest_type_id) : 1;
        $requests_created = [];

        for($i = 0; $i < $loop_length; $i++){
            if(is_archmage()){ // arcymag
                //non-bulk
                $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
                $client = ($rq->client_id) ? User::find($rq->client_id) : null;
                $price_data = StatsController::runPriceCalc($rq->price_code, $rq->client_id, true);

                $request = Request::create([
                    "made_by_me" => true,

                    "client_id" => $rq->client_id,
                    "client_name" => $rq->client_name,
                    "email" => $rq->email,
                    "phone" => $rq->phone,
                    "other_medium" => $rq->other_medium,
                    "contact_preference" => $rq->contact_preference ?? "email",

                    "song_id" => $rq->song_id,
                    "quest_type_id" => $rq->quest_type_id,
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => yt_cleanup($rq->link),
                    "genre_id" => $rq->genre_id,
                    "wishes" => $rq->wishes,

                    "price_code" => $price_data["labels"],
                    "price" => $price_data["price"],
                    "deadline" => $rq->deadline,
                    "hard_deadline" => $rq->hard_deadline,
                    "delayed_payment" => $rq->delayed_payment,
                    "status_id" => 1,
                ]);

                // nadpisanie zmienionych gotowców
                if($rq->song_id){
                    Song::find($rq->song_id)->update([
                        "title" => $rq->title,
                        "artist" => $rq->artist,
                        "link" => yt_cleanup($rq->link),
                        "genre_id" => $rq->genre_id,
                        "price_code" => preg_replace("/[=\-oyzqr\d]/", "", $rq->price_code),
                    ]);
                }
                if($rq->client_id){
                    User::find($rq->client_id)->update([
                        "client_name" => $rq->client_name,
                        "email" => $rq->email,
                        "phone" => $rq->phone,
                        "other_medium" => $rq->other_medium,
                        "contact_preference" => $rq->contact_preference,
                    ]);
                }

                BackController::newStatusLog($request->id, 1, $rq->comment);

                //mailing
                $mailing = null;
                if($request->contact_preference != "email"){
                    $mailing ??= false;
                    $flash_content .= ", ale wyślij wiadomość";
                }
                if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);
            }else{ // klient
                //bulk

                // ignore empty requests
                if (empty($rq->title) && empty($rq->artist) && empty($rq->link)) {
                    continue;
                }

                $request = Request::create([
                    "made_by_me" => false,

                    "client_id" => (Auth::check()) ? Auth::id() : null,
                    "client_name" => (Auth::check()) ? Auth::user()->notes->client_name : $rq->client_name,
                    "email" => (Auth::check()) ? Auth::user()->notes->email : $rq->email,
                    "phone" => (Auth::check()) ? Auth::user()->notes->phone : $rq->phone,
                    "other_medium" => (Auth::check()) ? Auth::user()->notes->other_medium : $rq->other_medium,
                    "contact_preference" => (Auth::check()) ? Auth::user()->notes->contact_preference : $rq->contact_preference,

                    "quest_type_id" => $rq->quest_type_id,
                    "title" => $rq->title,
                    "artist" => $rq->artist,
                    "link" => yt_cleanup($rq->link),
                    "wishes" => $rq->wishes,

                    "hard_deadline" => $rq->hard_deadline,
                    "status_id" => 1,
                ]);

                //mailing do mnie
                $mailing = null;
                Mail::to(env("MAIL_MAIN_ADDRESS"))->send(new ArchmageQuestMod($request->fresh()));
                $mailing = true;

                BackController::newStatusLog($request->id, 1, $rq->wishes, (Auth::check()) ? Auth::id() : null, $mailing);
            }
            $requests_created[] = $request;
        }

        if(Auth::check()) return redirect()->route("dashboard")->with("toast", ["success", $flash_content]);
        return view("pages.client.request-confirm", compact("requests_created"))->with("toast", ["success", $flash_content]);
    }

    public function processMod(HttpRequest $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        $intent = $rq->intent;
        $request = Request::find($rq->id);

        $is_same_status = $request->status_id == $rq->new_status;

        // archmage validation
        if (Auth::id() === 1 && $rq->new_status == 5) {
            // forgot to define genre
            if (!$rq->genre_id) {
                return back()->with("toast", ["error", "Uzupełnij gatunek"]);
            }

            // tries to send quote by mail to client without it
            if (!$rq->email && $rq->contact_preference == "email") {
                return back()->with("toast", ["error", "Brakuje adresu email klienta lub klient nie ma takiej preferencji"]);
            }
        }

        if($intent == "change"){
            $song = ($rq->song_id) ? Song::find($rq->song_id) : null;
            $client = ($rq->client_id) ? User::find($rq->client_id) : null;

            // price required when sending quote
            if(
                $rq->new_status == 5 &&
                (!$rq->price_code)
            ){
                return back()->with("toast", ["error", "Uzupełnij wycenę"]);
            }

            $price_data = StatsController::runPriceCalc($rq->price_code, $rq->client_id, true);

            $request->update([
                "client_id" => $rq->client_id,
                "client_name" => $rq->client_name,
                "email" => $rq->email,
                "phone" => $rq->phone,
                "other_medium" => $rq->other_medium,
                "contact_preference" => $rq->contact_preference ?? "email",

                "song_id" => $rq->song_id,
                "quest_type_id" => $rq->quest_type_id,
                "title" => $rq->title,
                "artist" => $rq->artist,
                "link" => yt_cleanup($rq->link),
                "genre_id" => $rq->genre_id,
                "wishes" => $rq->wishes,

                "price_code" => $price_data["labels"],
                "price" => $price_data["price"],
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
            BackController::newStatusLog($request->id, $request->status_id, $rq->comment, $changed_by, null, $changes);
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
        }else if($request->status_id != 4 && !is_archmage()){ // mail do mnie
            Mail::to(env("MAIL_MAIN_ADDRESS"))->send(new ArchmageQuestMod($request->fresh()));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }
        if($mailing !== null) $request->history->first()->update(["mail_sent" => $mailing]);

        return redirect()->route("request", ["id" => $request->id])->with("toast", ["success", $flash_content]);
    }

    public function finalize($id, $status, $with_priority = false){
        $request = Request::findOrFail($id);

        if(in_array($request->status_id, [4,7,8,9]))
            return redirect()->route("request", ["id" => $id])->with("toast", ["error", "Zapytanie już zamknięte"]);

        $request->status_id = $status;
        $price = StatsController::runPriceCalc($request->price_code.(($with_priority) ? "z" : ""), $request->client_id, true);

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
                $song->save();

                $request->song_id = $song->id;
            }
            //add new client if not exists
            if(!$request->client_id){
                $is_new_client = 1;

                $password = generate_password();

                $client = User::create([
                    "name" => substr($password, 0, AuthController::NOLOGIN_LOGIN_PART_LENGTH),
                    "email" => $request->email ?? Str::uuid()."@test.test",
                    "password" => $password,
                ]);
                $client->roles()->attach("client");
                $client->notes()->create([
                    "password" => $password,
                    "client_name" => $request->client_name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "other_medium" => $request->other_medium,
                    "contact_preference" => $request->contact_preference,
                ]);

                $request->client_id = $client->id;

                //bind remaining anonymous quests from the same guy
                Request::whereIn("status_id", [1, 5])
                    ->where("client_name", $request->client_name)
                    ->where("email", $request->email)
                    ->where("phone", $request->phone)
                    ->where("other_medium", $request->other_medium)
                    ->where("contact_preference", $request->contact_preference)
                    ->update(["client_id" => $client->id]);
            }else{
                $client = User::find($request->client_id);
            }

            $quest = new Quest;
            $quest->id = next_quest_id($request->quest_type_id);
            $quest->song_id = $request->song_id ?? $song->id;
            $quest->client_id = $request->client_id ?? $client->id;
            $quest->status_id = 11;
            $quest->price_code_override = $price["labels"];
            $quest->price = $price["price"];
            $quest->deadline = ($with_priority) ? get_next_working_day() : $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->delayed_payment = $request->delayed_payment;
            $quest->wishes = $request->wishes;
            $quest->save();

            // $invoice = Invoice::create([
            //     "quest_id" => $quest->id,
            //     "amount" => $quest->price
            // ]);

            if($client->notes->budget){
                $sub_amount = min([$request->price, $client->notes->budget]);
                $client->notes->update([
                    "budget" => $client->notes->budget - $sub_amount,
                ]);
                BackController::newStatusLog(null, 32, -$sub_amount, $client->id);
                MoneyTransaction::create([
                    "typable_type" => IncomeType::class,
                    "typable_id" => 2,
                    "relatable_type" => User::class,
                    "relatable_id" => $client->id,
                    "date" => today(),
                    "amount" => -$sub_amount,
                ]);
                if($sub_amount == $request->price){
                    $quest->paid = true;
                    $quest->save();
                }
                BackController::newStatusLog($quest->id, 32, $sub_amount, $client->id);
                MoneyTransaction::create([
                    "typable_type" => IncomeType::class,
                    "typable_id" => 1,
                    "relatable_type" => Quest::class,
                    "relatable_id" => $quest->id,
                    "date" => today(),
                    "amount" => $sub_amount,
                ]);
                // $invoice->update(["paid" => $sub_amount]);
            }

            $request->quest_id = $quest->id;
            $request->price_code = $price["labels"];
            $request->price = $price["price"];
        }

        $request->save();
        $request = $request->fresh();

        //mail do mnie, bo zmiany w zapytaniu
        $mailing = null;
        Mail::to(env("MAIL_MAIN_ADDRESS"))->send(new ArchmageQuestMod($request->fresh()));
        $mailing = true;

        BackController::newStatusLog($id, $status, null, (is_archmage()) ? $request->client_id : null, $mailing);

        if ($status == 9){
            //added quest
            BackController::newStatusLog($request->quest_id, 11, null, $request->client_id);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
            //send onboarding if new client
            if ($request->user->notes->email && $is_new_client){
                Mail::to($request->user->notes->email)->send(new Onboarding($request->user));
            }
        }

        if(is_archmage()) return redirect()->route("request", ["id" => $request->id]);
        else return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
    }

    public function questReject(HttpRequest $rq){
        //adding comment
        $history = StatusChange::where("re_quest_id", $rq->id)
            ->where("new_status_id", $rq->status)
            ->first();

        $history->values = json_encode(["powód" => $rq->comment]);
        $history->save();

        $where_to = (!Auth::check()) ? "home" : "dashboard";
        return redirect()->route($where_to)->with("toast", ["success", "Komentarz dodany"]);
    }

    #region select
    public function selectUser(HttpRequest $rq)
    {
        $user = User::findOrFail($rq->client_id);
        $request = Request::findOrFail($rq->request_id);

        $request->update([
            "client_id" => $user->id,
            "client_name" => $user->notes->client_name,
            "email" => $user->notes->email,
            "phone" => $user->notes->phone,
            "other_medium" => $user->notes->other_medium,
            "contact_preference" => $user->notes->contact_preference,
        ]);

        return back()->with("toast", ["success", "Utwór przypisany"]);
    }

    public function selectSong(HttpRequest $rq)
    {
        $song = Song::findOrFail($rq->song_id);
        $request = Request::findOrFail($rq->request_id);

        $request->update([
            "song_id" => $song->id,
            "title" => $song->title,
            "artist" => $song->artist,
            "link" => $song->link,
            "notes" => $song->notes,
            "genre_id" => $song->genre_id,
            "quest_type_id" => $song->type->id,
        ]);

        return back()->with("toast", ["success", "Utwór przypisany"]);
    }
    #endregion
}
