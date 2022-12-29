<?php

namespace App\Http\Controllers;

use App\Mail\ArchmageQuestMod;
use App\Mail\PaymentReceived;
use App\Mail\QuestUpdated;
use App\Mail\RequestQuoted;
use App\Models\Client;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackController extends Controller
{
    public function dashboard(){
        $client = Auth::user()->client;

        $requests = Request::whereNotIn("status_id", [4, 7, 8, 9])
            ->orderByRaw("case when deadline is null then 1 else 0 end")
            ->orderBy("deadline");
        $quests = Quest::whereNotIn("status_id", [17, 18, 19])
        ->orderByRaw("price_code_override not regexp 'z'") //najpierw priorytety
        ->orderByRaw("case status_id when 12 then 1 when 16 then 2 when 11 then 3 when 26 then 4 when 15 then 5 when 13 then 6 else 7 end")
        ->orderByRaw("paid desc")
        ->orderByRaw("case when deadline is null then 1 else 0 end")
        ->orderBy("deadline")
            ;
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
            $quests_total = client_exp(Auth::id());
        }else{
            $patrons_adepts = Client::where("helped_showcasing", 1)->get();
            $unpaids = Quest::where("paid", 0)
                ->whereNotIn("status_id", [18])
                ->whereHas("client", function(Builder $query){
                    $query->where("trust", ">", -1);
                })
                ->orderBy("quests.updated_at")
                ->get();
            $gains = [
                "this_month" => StatusChange::where("new_status_id", 32)->whereMonth("date", Carbon::today()->month)->sum("comment"),
                "total" => StatusChange::where("new_status_id", 32)->sum("comment"),
            ];
        }
        $requests = $requests->get();
        $quests = $quests->get();

        return view(user_role().".dashboard", array_merge(
            ["title" => (Auth::id() == 1) ? "Szpica arcymaga" : "Pulpit"],
            compact("quests", "requests"),
            (isset($quests_total) ? compact("quests_total") : []),
            (isset($patrons_adepts) ? compact("patrons_adepts") : []),
            (isset($unpaids) ? compact("unpaids") : []),
            (isset($gains) ? compact("gains") : []),
        ));
    }

    public function prices(){
        $prices = DB::table("prices")->get();

        $discount = (Auth::id() == 1) ? null : (
            is_veteran(Auth::id()) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
            +
            is_patron(Auth::id()) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
        );

        return view(user_role().".prices", array_merge(
            ["title" => "Cennik"],
            compact("prices", "discount")
        ));
    }

    public function clients(){
        $clients_raw = Client::all();
        $clients_count = count($clients_raw);
        $max_exp = 0;
        $classes = ["1. Weterani", "2. Biegli", "3. Zainteresowani", "4. Nowicjusze", "5. Debiutanci"];

        foreach($clients_raw as $client){
            $client->exp = client_exp($client->id);
            if($client->exp > $max_exp) $max_exp = $client->exp;

            if(is_veteran($client->id)) $class = $classes[0];
            elseif(client_exp($client->id) >= 4) $class = $classes[1];
            elseif(client_exp($client->id) >= 2) $class = $classes[2];
            elseif(client_exp($client->id) >= 1) $class = $classes[3];
            else $class = $classes[4];

            $clients[$class][] = $client;
        }
        ksort($clients);
        foreach($clients as $k => $v){
            $clients[$k] = collect($v)->sortBy([['exp', "desc"], ['client_name', 'asc']]);
        }

        return view(user_role().".clients", array_merge(
            ["title" => "Klienci"],
            compact("clients", "max_exp","classes", "clients_count")
        ));
    }

    public function requests(){
        $client = Auth::user()->client;

        $requests = Request::orderBy("updated_at", "desc");
        if(Auth::id() != 1){
            $requests = $requests->where("client_id", $client->id);
        }
        $requests = $requests->paginate(25);

        return view(user_role().".requests", [
            "title" => "Lista zapytań",
            "requests" => $requests,
        ]);
    }
    public function request($id){
        $request = Request::findOrFail($id);

        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"] ." (". ($client["email"] ?? $client["phone"]) .")";
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = "$song[title] ($song[artist])";
            }
        }else{
            if($request->client_id != Auth::id()) abort(403, "To nie jest Twoje zapytanie");
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->where("quest_type_id", $request->quest_type_id)->orWhereNull("quest_type_id")->orderBy("indicator")->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".request", array_merge([
            "title" => "Zapytanie",
        ], compact("request", "prices", "questTypes", "clients", "songs", "genres")));
    }
    public function addRequest(){
        if(Auth::id() == 1){
            $clients_raw = Client::all()->toArray();
            foreach($clients_raw as $client){
                $clients[$client["id"]] = $client["client_name"] ." (". ($client["email"] ?? $client["phone"]) .")";
            }
            $songs_raw = Song::all()->toArray();
            foreach($songs_raw as $song){
                $songs[$song["id"]] = "$song[title] ($song[artist])";
            }
        }else{
            $clients = [];
            $songs = [];
        }

        $questTypes_raw = QuestType::all()->toArray();
        foreach($questTypes_raw as $val){
            $questTypes[$val["id"]] = $val["type"];
        }

        $prices = DB::table("prices")->orderBy("quest_type_id")->pluck("service", "indicator")->toArray();
        $genres = DB::table("genres")->pluck("name", "id")->toArray();

        return view(user_role().".add-request", array_merge([
            "title" => "Nowe zapytanie"
        ], compact("questTypes", "prices", "clients", "songs", "genres")));
    }

    public function modRequestBack(HttpRequest $rq){
        $modifying = $rq->modifying; //insert -- 0; update -- request_id
        $request = ($modifying != 0) ? Request::find($modifying) : new Request;

        if(Auth::id() != 1){
            // składanie requesta przez klienta
            if(Auth::check()){
                $request->client_id = Auth::user()->client->id;
            }else if(!$modifying){
                if($rq->m_test != 20) return redirect()->route("home")->with("error", "Cztery razy pięć nie równa się $rq->m_test");
                $request->client_name = $rq->client_name;
                $request->email = $rq->email;
                $request->phone = $rq->phone;
                $request->other_medium = $rq->other_medium;
                $request->contact_preference = $rq->contact_preference;
            }
            if($request->made_by_me === null) $request->made_by_me = false;
            $request->quest_type_id = $rq->quest_type;
            $request->title = $rq->title;
            $request->artist = $rq->artist;
            $request->link = $rq->link;
            $request->wishes = $rq->wishes;
            $request->hard_deadline = $rq->hard_deadline;
            if($rq->new_status == 1){
                $request->price_code = null;
                $request->price = null;
            }
        }else{
            // składanie requesta przeze mnie
            if($request->made_by_me === null) $request->made_by_me = true;
            if($rq->client_id){
                $request->client_id = $rq->client_id;
                $client = Client::find($rq->client_id);
                $request->client_name = $client->client_name;
                $request->email = $client->email;
                $request->phone = $client->phone;
                $request->other_medium = $client->other_medium;
                $request->contact_preference = $client->contact_preference;
            }else{
                $request->client_name = $rq->client_name;
                $request->email = $rq->email;
                $request->phone = $rq->phone;
                $request->other_medium = $rq->other_medium;
                $request->contact_preference = $rq->contact_preference ?? "email";
            }
            if($rq->has("bind_with_song")){
                $request->song_id = $rq->song_id;
                $song = Song::find($rq->song_id);
                $request->quest_type_id = song_quest_type($rq->song_id)->id;
                $request->title = $song->title;
                $request->artist = $song->artist;
                $request->link = $song->link;
                $request->genre_id = $song->genre_id;
                $request->wishes = $song->wishes;
            }else{
                $request->quest_type_id = $rq->quest_type;
                $request->title = $rq->title;
                $request->artist = $rq->artist;
                $request->link = $rq->link;
                $request->genre_id = $rq->genre_id;
                $request->wishes = $rq->wishes;
            }
            $request->price_code = ($rq->new_status != 1) ? $rq->price_code : null;
            $request->price = ($rq->new_status != 1) ? price_calc($rq->price_code, $rq->client_id)[0] : null;
        }
        $request->deadline = ($rq->new_status != 1) ? $rq->deadline : null;

        $request->status_id = $rq->new_status;

        //zbierz zmiany przed dodaniem
        $comment = null;
        if(in_array($request->status_id, [5, 6]) && $modifying){
            $changes = [];
            $keys = array_keys(Arr::except($request->getDirty(), ["updated_at", "status_id"]));
            $pre = $request->getOriginal();
            $post = $request->getDirty();
            foreach($keys as $key){
                $changes[$key] = $pre[$key] . " → " . $post[$key];
            }
            $comment = json_encode($changes);
            if($comment == "[]") $comment = null;
        }

        $request->save();

        // sending mail
        $flash_content = "Zapytanie gotowe";
        $mailing = null;
        if($request->status_id == 5){
            //mail do klienta, bo wycena oddana
            if($request->email){
                Mail::to($request->email)->send(new RequestQuoted($request));
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }else{
                $mailing = false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if($request->status_id != 4){
            //mail do mnie, bo zmiany w zapytaniu
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }

        $this->statusHistory($request->id, $request->status_id, $comment, null, $mailing);

        return redirect()->route("request", ["id" => $request->id])->with("success", $flash_content);
    }

    public function requestFinal($id, $status){
        $request = Request::findOrFail($id);

        if(in_array($request->status_id, [4,7,8,9]))
            return redirect()->route("request", ["id" => $id])->with("error", "Zapytanie już zamknięte");

        $request->status_id = $status;

        $is_new_client = 0;

        // adding new quest
        if($status == 9){
            //add new song if not exists
            if(!$request->song_id){
                $song = new Song;
                $song->id = next_song_id($request->quest_type_id);
                $song->title = $request->title;
                $song->artist = $request->artist;
                $song->link = $request->link;
                $song->genre_id = $request->genre_id;
                $song->price_code = $request->price_code;
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
            }else{
                $client = Client::find($request->client_id);
            }

            $quest = new Quest;
            $quest->id = next_quest_id($request->quest_type_id);
            $quest->song_id = $request->song_id ?? $song->id;
            $quest->client_id = $request->client_id ?? $user->id;
            $quest->status_id = 11;
            $quest->price_code_override = $request->price_code;
            $quest->price = $request->price;
            $quest->deadline = $request->deadline;
            $quest->hard_deadline = $request->hard_deadline;
            $quest->save();
            if($client->budget >= $request->price){
                $client->budget -= $request->price;
                $client->save();
                $this->statusHistory($quest->id, 32, $request->price);
            }

            $request->quest_id = $quest->id;
        }

        $request->save();

        //mail do mnie, bo zmiany w zapytaniu
        $mailing = null;
        Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($request));
        $mailing = true;
        $this->statusHistory($id, $status, null, null, $mailing);

        if($status == 9){
            //added quest
            $this->statusHistory($request->quest_id, 11, null, $request->client_id);
            //add client ID to history
            StatusChange::whereIn("re_quest_id", [$request->id, $request->quest_id])->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
        }
        if($status == 8){
            //add client ID to history
            StatusChange::where("re_quest_id", $request->id)->whereNull("changed_by")->update(["changed_by" => $request->client_id]);
        }

        return redirect()->route("request-finalized", compact("id", "status", "is_new_client"));
    }

    public function statusHistory($re_quest_id, $new_status_id, $comment, $changed_by = null, $mailing = null){
        StatusChange::insert([
            "re_quest_id" => $re_quest_id,
            "new_status_id" => $new_status_id,
            "changed_by" => ($changed_by != null) ? $changed_by : Auth::id(),
            "comment" => $comment,
            "mail_sent" => $mailing,
            "date" => now(),
        ]);
    }
    public function questReject(HttpRequest $rq){
        //adding comment
        $history = StatusChange::where("re_quest_id", $rq->id)
            ->where("new_status_id", $rq->status)
            ->first();

        $history->comment = json_encode(["powód" => $rq->comment]);
        $history->save();

        $where_to = (!Auth::check()) ? "home" : "dashboard";
        return redirect()->route($where_to)->with("success", "Komentarz dodany");
    }

    public function quests(){
        $client = Auth::user()->client;

        $quests = Quest::orderBy("quests.created_at", "desc");
        if(Auth::id() != 1){
            $quests = $quests->where("client_id", $client->id);
        }
        $quests = $quests->paginate(25);

        return view(user_role().".quests", [
            "title" => "Lista zleceń",
            "quests" => $quests
        ]);
    }
    public function quest($id){
        $quest = Quest::findOrFail($id);

        $prices = DB::table("prices")->where("quest_type_id", song_quest_type($quest->song_id)->id)->orWhereNull("quest_type_id")->orderBy("indicator")->pluck("service", "indicator")->toArray();
        if(Auth::id() == 1) $stats_statuses = DB::table("statuses")->where("id", ">=", 100)->get()->toArray();
        else if($quest->client_id != Auth::id()) abort(403, "To nie jest Twoje zlecenie");

        $files_raw = Storage::files('safe/'.$id);
        $files = []; $last_mod = []; $desc = [];

        if(!empty($files_raw)){
            foreach($files_raw as $file){
                $name = preg_split('/(\=|\_)/', pathinfo($file, PATHINFO_FILENAME));
                if(!isset($name[2])) $name[2] = "wersja zero";
                $files[$name[0]][$name[1]][$name[2]][] = $file;
                $last_mod[$name[1]][$name[2]] = Storage::lastModified($file);
                if(pathinfo($file, PATHINFO_EXTENSION) == "md") $desc[$name[1]][$name[2]] = $file;
            }
        }

        $workhistory = SongWorkTime::where("song_id", $quest->song_id)->orderBy("status_id")->get();

        return view(
            user_role().".quest",
            array_merge(
                ["title" => "Zlecenie"],
                compact("quest", "prices", "files", "last_mod", "desc"),
                (isset($stats_statuses) ? compact("stats_statuses") : []),
                (isset($workhistory) ? compact("workhistory") : []),
            )
        );
    }
    public function modQuestBack(HttpRequest $rq){
        $quest = Quest::findOrFail($rq->quest_id);
        if(SongWorkTime::where(["song_id" => $quest->song_id, "now_working" => 1])->first()){
            return back()->with("error", "Zatrzymaj zegar");
        }

        // wpisywanie wpłaty za zlecenie
        if($rq->status_id == 32){
            if(empty($rq->comment)) return redirect()->route("quest", ["id" => $rq->quest_id])->with("error", "Nie podałeś ceny");
            $this->statusHistory($rq->quest_id, $rq->status_id, $rq->comment, $quest->client_id);
            $quest->update(["paid" => (StatusChange::where(["new_status_id" => $rq->status_id, "re_quest_id" => $quest->id])->sum("comment") >= $quest->price)]);

            // sending mail
            $flash_content = "Cena wpisana";
            if($quest->paid){
                if($quest->client->email){
                    Mail::to($quest->client->email)->send(new PaymentReceived($quest));
                    StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => true]);
                    $flash_content .= ", mail wysłany";
                }else{
                    StatusChange::where(["re_quest_id" => $rq->quest_id, "new_status_id" => $rq->status_id])->first()->update(["mail_sent" => false]);
                    $flash_content .= ", ale wyślij wiadomość";
                }
            }

            return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
        }

        $quest->status_id = $rq->status_id;
        $quest->save();

        // sending mail
        $flash_content = "Faza zmieniona";
        $mailing = null;
        if($quest->status_id == 15){
            //mail do klienta, bo oddaję zlecenie
            if($quest->client->email){
                Mail::to($quest->client->email)->send(new QuestUpdated($quest));
                $mailing = true;
                $flash_content .= ", mail wysłany";
            }else{
                $mailing = false;
                $flash_content .= ", ale wyślij wiadomość";
            }
        }else if(Auth::id() != 1){
            //mail do mnie, bo zmiany w zleceniu
            Mail::to("kontakt@muzykaszytanamiare.pl")->send(new ArchmageQuestMod($quest));
            $mailing = true;
            $flash_content .= ", mail wysłany";
        }

        $this->statusHistory($rq->quest_id, $rq->status_id, $rq->comment, null, $mailing);

        return redirect()->route("quest", ["id" => $rq->quest_id])->with("success", $flash_content);
    }

    public function workClock(HttpRequest $rq){
        $now_working = SongWorkTime::where("now_working", 1)->first();

        if($now_working){
            $now_working->update([
                "now_working" => 0,
                "time_spent" => Carbon::createFromTimeString($now_working->time_spent)
                        ->addSeconds(Carbon::createFromTimeString($now_working->since)->diffInSeconds(now()))
                        ->format("H:i:s")
            ]);
        }

        if($rq->status_id != 13){
            SongWorkTime::updateOrCreate(["status_id" => $rq->status_id], [
                "song_id" => $rq->song_id,
                "status_id" => $rq->status_id,
                "now_working" => 1,
                "since" => now(),
            ]);
        }

        return back()->with("success", "Praca zalogowana");
    }

    public function songs(){
        $songs = Song::orderBy("title")
            ->get();
        $songs_count = count($songs);

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
            compact("songs", "songs_count", "song_work_times", "price_codes")
        ));
    }

    public function showcases(){
        $showcases = Showcase::orderBy("song_id", "desc")->get();
        $showcases_count = count($showcases);

        $songs_raw = Song::all()->toArray();
        foreach($songs_raw as $song){
            $songs[$song["id"]] = "$song[title] ($song[artist]) [$song[id]]";
        }

        return view(user_role().".showcases", array_merge(
            ["title" => "Lista reklam"],
            compact("showcases", "showcases_count", "songs")
        ));
    }

    public function addShowcase(HttpRequest $rq){
        Showcase::insert([
            "song_id" => $rq->song_id,
            "link_fb" => $rq->link_fb,
            "link_ig" => $rq->link_ig,
        ]);

        return back()->with("success", "Dodano pozycję");
    }

    public function janitorLog(){
        $logs = StatusChange::whereIn("new_status_id", [7, 17, 33])
            ->whereDate('date', '>=', now()->subDays(5)->setTime(0,0,0)->toDateTimeString())
            ->get();

        return view(user_role().".janitor-log", array_merge(
            ["title" => "Logi Sprzątacza"],
            compact("logs")
        ));
    }

    public function ppp(){
        $questions = [
            "Podkład MIDI" => "Podkłady MIDI traktuję jako **nuty**"
        ];
        ksort($questions);

        return view(user_role().".ppp", array_merge(
            ["title" => "Poradnik Przyszłych Pokoleń"],
            compact("questions")
        ));
    }
}
