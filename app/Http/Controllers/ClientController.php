<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function view($id){
        $client = Client::findOrFail($id);

        return view(user_role().".client", array_merge([
            "title" => $client->client_name
        ], compact("client")));
    }

    public function edit($id, Request $rq){
        Client::findOrFail($id)->update([
            "client_name" => $rq->client_name,
            "email" => $rq->email,
            "phone" => $rq->phone,
            "other_medium" => $rq->other_medium,
            "contact_preference" => $rq->contact_preference,
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

        return back()->with("success", "Dane poprawione");
    }
}
