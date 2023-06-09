<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function view($id){
        $client = Client::findOrFail($id);

        return view(user_role().".client", array_merge([
            "title" => $client->client_name
        ], compact("client")));
    }
}
