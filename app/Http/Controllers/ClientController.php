<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function view($id){
        $client = Client::findOrFail($id);
        if(!in_array(Auth::id(), [1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        return view(user_role().".client", array_merge([
            "title" => $client->client_name." | Edycja klienta"
        ], compact("client")));
    }

    public function edit($id, Request $rq){
        if(!in_array(Auth::id(), [1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        Client::findOrFail($id)->update([
            "client_name" => $rq->client_name,
            "email" => $rq->email,
            "phone" => $rq->phone,
            "other_medium" => $rq->other_medium,
            "contact_preference" => $rq->contact_preference,
        ]);

        if(Auth::id() == 1){
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
