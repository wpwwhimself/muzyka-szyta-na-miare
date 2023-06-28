<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $client->exp = client_exp($client->id);
            if($client->exp > $max_exp) $max_exp = $client->exp;

            if(is_veteran($client->id)) $class = $classes[0];
            elseif(client_exp($client->id) >= 4) $class = $classes[1];
            elseif(client_exp($client->id) >= 2) $class = $classes[2];
            elseif(client_exp($client->id) >= 1) $class = $classes[3];
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
        $client = Client::findOrFail($id);
        if(!in_array(Auth::id(), [0, 1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        return view(user_role().".client", array_merge([
            "title" => $client->client_name." | Edycja klienta"
        ], compact("client")));
    }

    public function edit($id, Request $rq){
        if(!in_array(Auth::id(), [0, 1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        Client::findOrFail($id)->update([
            "client_name" => $rq->client_name,
            "email" => $rq->email,
            "phone" => $rq->phone,
            "other_medium" => $rq->other_medium,
            "contact_preference" => $rq->contact_preference,
        ]);

        if(Auth::id() <= 1){
            Client::findOrFail($id)->update([
                "trust" => $rq->trust,
                "helped_showcasing" => $rq->helped_showcasing,
                "budget" => $rq->budget,
                "extra_exp" => $rq->extra_exp,
                "default_wishes" => $rq->default_wishes,
                "special_prices" => $rq->special_prices,
            ]);
            User::findOrFail($id)->update([
                "password" => $rq->password,
            ]);
        }

        return back()->with("success", "Dane poprawione");
    }
}
