<?php

namespace App\Http\Controllers;

use App\Mail\CustomMail;
use App\Models\Client;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function list(Request $rq, $param = null, $value = 0){
        $search = strtolower($rq->search ?? "");

        $clients_raw = ($param) ?
            Client::where($param, (in_array($param, ["budget", "helped_showcasing"]) ? ">" : "="), $value) :
            Client::whereNotNull("client_name")
        ;
        $clients_raw = $clients_raw
            ->where(fn($q) => $q
                ->whereRaw("LOWER(client_name) like '%$search%'")
                ->orWhereRaw("CONVERT(phone, CHAR) like '%$search%'")
                ->orWhereRaw("LOWER(email) like '%$search%'")
                ->orWhereRaw("CONVERT(id, CHAR) like '%$search%'")
            );
        $clients_raw = $clients_raw->get();

        $max_exp = 0;
        $classes = ["1. Weterani", "2. Biegli", "3. Zainteresowani", "4. Nowicjusze", "5. Debiutanci"];

        $clients = [];
        foreach($clients_raw as $client){
            $client->exp = $client->exp;
            if($client->exp > $max_exp) $max_exp = $client->exp;

            if($client->is_veteran) $class = $classes[0];
            elseif($client->exp >= 4) $class = $classes[1];
            elseif($client->exp >= 2) $class = $classes[2];
            elseif($client->exp >= 1) $class = $classes[3];
            else $class = $classes[4];

            $clients[$class][] = $client;
        }
        if($clients) ksort($clients);
        foreach($clients as $k => $v){
            $clients[$k] = collect($v)->sortBy([['exp', "desc"], ['client_name', 'asc']]);
        }

        return view(user_role().".clients", array_merge(
            ["title" => "Klienci"],
            compact("clients", "max_exp","classes", "search")
        ));
    }

    public function view($id){
        $client = Client::find($id);
        if(!$client) abort(404, "Nie ma takiego klienta");
        if(!in_array(Auth::id(), [0, 1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        $contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne",
        ];

        return view(user_role().".client", array_merge([
            "title" => $client->client_name." | Edycja klienta"
        ], compact("client", "contact_preferences")));
    }

    public function edit($id, Request $rq){
        if(Auth::id() === 0) return back()->with("error", OBSERVER_ERROR());
        if(!in_array(Auth::id(), [1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        $client = Client::find($id);
        if(!$client) abort(404, "Nie ma takiego klienta");
        $client->update([
            "client_name" => $rq->client_name,
            "email" => $rq->email,
            "phone" => $rq->phone,
            "other_medium" => $rq->other_medium,
            "contact_preference" => $rq->contact_preference,
        ]);

        if(is_archmage()){
            $client->update([
                "trust" => $rq->trust,
                "helped_showcasing" => $rq->helped_showcasing,
                "extra_exp" => $rq->extra_exp,
                "default_wishes" => $rq->default_wishes,
                "special_prices" => $rq->special_prices,
                "external_drive" => $rq->external_drive,
            ]);
            $client->user->update([
                "password" => $rq->password,
            ]);

            // budget handling
            if($client->budget != $rq->budget){
                BackController::newStatusLog(
                    null,
                    32,
                    $rq->budget - $client->budget,
                    $client->id
                );
                $client->update(["budget" => $rq->budget]);
            }
        }

        return back()->with("success", "Dane poprawione");
    }

    #region mailing
    public function mailPrepare(?int $client_id = null)
    {
        $clients = Client::orderBy("client_name")
            ->whereNotNull("email")
            ->get()
            ->mapWithKeys(fn ($cl) => [$cl->id => "$cl->client_name ($cl->email)"])
            ->toArray();

        return view(user_role().".mail.prepare", compact("clients", "client_id"));
    }

    public function mailSend(Request $rq)
    {
        $failures = 0;

        $clients_for_mailing = $rq->clients
            ? Client::whereIn("id", $rq->clients)->get()
            : Client::whereNotNull("email")->get(); // defaults to everybody available!

        foreach ($clients_for_mailing as $client) {
            try {
                Mail::to($client->email)
                    ->send(new CustomMail($client, $rq->subject, $rq->content));
            } catch (Exception $e) {
                $failures++;
            }
        }

        return back()->with("success", "Mail wysłany" . ($failures ? ", błędów: $failures" : ""));
    }
    #endregion

    //////////////////////////////////////////

    public function getById(int $id){
        $data = Client::find($id)->toArray();
        foreach($data as $key => $value){
            if(!preg_match("/id/", $key)) $data[$key] = _ct_($value);
        }
        return json_encode($data);
    }
}
