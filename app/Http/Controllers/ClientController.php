<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shipyard\AuthController;
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
    public function list(Request $rq, $param = null, $value = 0){
        if (is_archmage()) {
            return redirect()->route("admin.model.list", ["model" => "user-notes"])->withInput();
        }
    }

    public function view($id){
        $client = User::findOrFail($id);
        if(!in_array(Auth::id(), [0, 1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        return view("pages.".user_role().".client", array_merge([
            "title" => $client->client_name." | Edycja klienta"
        ], compact(
            "client",
        )));
    }

    public function edit($id, Request $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        if(!in_array(Auth::id(), [1, $id])) abort(403, "Nie możesz edytować danych innego użytkownika");

        $client = User::findOrFail($id);
        if ($rq->email) {
            $client->update([
                "email" => $rq->email,
            ]);
        }
        $client->notes()->update([
            "client_name" => $rq->client_name,
            "email" => $rq->email,
            "phone" => $rq->phone,
            "other_medium" => $rq->other_medium,
            "contact_preference" => $rq->contact_preference,
        ]);

        if(is_archmage()){
            $client->notes()->update([
                "trust" => $rq->trust,
                "is_forgotten" => $rq->has("is_forgotten"),
                "helped_showcasing" => $rq->helped_showcasing,
                "extra_exp" => $rq->extra_exp,
                "default_wishes" => $rq->default_wishes,
                "special_prices" => $rq->special_prices,
                "external_drive" => $rq->external_drive,
                "password" => $rq->password,
            ]);
            $client->update([
                "name" => substr($rq->password, 0, AuthController::NOLOGIN_LOGIN_PART_LENGTH),
                "password" => Hash::make($rq->password),
            ]);

            if ($rq->has("pinned_comment_id")) {
                $client->notes()->update(["helped_showcasing" => 2]);
                StatusChange::where("changed_by", $client->id)->update(["pinned" => false]);
                StatusChange::find($rq->pinned_comment_id)->update(["pinned" => true]);
            }

            // budget handling
            if($client->notes->budget != $rq->budget){
                BackController::newStatusLog(
                    null,
                    32,
                    $rq->budget - $client->notes->budget,
                    $client->id
                );
                MoneyTransaction::create([
                    "typable_type" => IncomeType::class,
                    "typable_id" => 2,
                    "relatable_type" => User::class,
                    "relatable_id" => $client->id,
                    "amount" => $rq->budget - $client->notes->budget,
                    "date" => today(),
                ]);
                $client->notes()->update(["budget" => $rq->budget]);
            }
        }

        return back()->with("toast", ["success", "Dane poprawione"]);
    }

    #region mailing
    public function mailPrepare(?int $client_id = null)
    {
        $clients = User::clients()
            ->orderBy("client_name")
            ->whereNotNull("email")
            ->get()
            ->mapWithKeys(fn ($cl) => [$cl->id => "$cl->client_name ($cl->email)"])
            ->toArray();

        return view("pages.".user_role().".mail.prepare", compact("clients", "client_id"));
    }

    public function mailSend(Request $rq)
    {
        $failures = 0;

        $clients_for_mailing = $rq->clients
            ? User::whereIn("id", $rq->clients)->get()
            : User::whereNotNull("email")->get(); // defaults to everybody available!

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
