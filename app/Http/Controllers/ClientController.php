<?php

namespace App\Http\Controllers;

use Wpwwhimself\Shipyard\Controllers\AuthController;
use App\Mail\CustomMail;
use App\Models\IncomeType;
use App\Models\MoneyTransaction;
use App\Models\StatusChange;
use App\Models\Top10;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    #region mailing
    public function mailPrepare(?int $client_id = null)
    {
        $clients = User::orderBy("display_name")
            ->mailableClients()
            ->get()
            ->map(fn ($cl) => ["value" => $cl->id, "label" => "$cl->display_name ($cl->email)"])
            ->toArray();

        return view("pages.".user_role().".mail.prepare", compact("clients", "client_id"));
    }

    public function mailSend(Request $rq)
    {
        $failures = 0;

        $clients_for_mailing = $rq->clients
            ? User::whereIn("id", $rq->clients)->get()
            : User::mailableClients()->get(); // defaults to everybody available!

        foreach ($clients_for_mailing as $client) {
            try {
                Mail::to($client->email)
                    ->send(new CustomMail($client, $rq->subject, $rq->content));
            } catch (Exception $e) {
                $failures++;
            }
        }

        return back()->with("toast", ["success", "Mail wysłany" . ($failures ? ", błędów: $failures" : "")]);
    }
    #endregion

    //////////////////////////////////////////

    public function getById(int $id){
        $data = User::find($id)->toArray();
        foreach($data as $key => $value){
            if(!preg_match("/id/", $key)) $data[$key] = _ct_($value);
        }
        return json_encode($data);
    }
}
