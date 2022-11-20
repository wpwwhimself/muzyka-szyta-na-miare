<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\HomeController;
use App\Models\Client;
use App\Models\Genre;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, "index"])->name("home");

Route::controller(AuthController::class)->group(function(){
    Route::get('/auth', "input")->name("login");
    Route::post('/auth-back', "authenticate")->name("authenticate");
    Route::post('/auth/register-back', "register")->name("register");
    Route::get('/auth/logout', "logout")->name("logout");
});

Route::controller(BackController::class)->group(function(){
    Route::get('/dashboard', "dashboard")->middleware("auth")->name("dashboard");

    Route::get('/quests', "quests")->middleware("auth")->name("quests");
    Route::get('/quests/view/{id}', "quest")->middleware("auth")->name("quest");
    Route::get('/quests/add', "addQuest")->middleware("auth")->name("add-quest");
    Route::post('/quests/add-back', "addQuestBack")->middleware("auth")->name("add-quest-back");
    Route::post('/quests/mod-back', "modQuestBack")->middleware("auth")->name("mod-quest-back");

    Route::get('/requests', "requests")->middleware("auth")->name("requests");
    Route::get('/requests/view/{id}', "request")->name("request");
    Route::get('/requests/add', "addRequest")->middleware("auth")->name("add-request");
    Route::post('/requests/mod-back', "modRequestBack")->name("mod-request-back");

    Route::get('/requests/finalize/{id}/{status}', "requestFinal")->name("request-final");

    Route::get('/clients', "clients")->middleware("auth")->name("clients");
    Route::get('/clients/view/{id}', "client")->middleware("auth")->name("client");

    Route::get('/ads', "ads")->middleware("auth")->name("ads");
    Route::get('/messages', "messages")->middleware("auth")->name("messages");

    Route::post("/request-finalized-sub", "questReject")->name("quest-reject");
});

Route::get('/request-finalized/{id}/{status}/{is_new_client}', function($id, $status, $is_new_client){
    return view("request-finalized", array_merge(
        ["title" => "Zapytanie zamknięte"],
        compact("id", "status", "is_new_client")
    ));
})->name("request-finalized");
Route::get("/patron-mode/{id}", function($id){
    Client::findOrFail($id)->update(["helped_showcasing" => 1]);
    return redirect()->route("dashboard")->with("success", "Wystawienie opinii odnotowane");
})->name("patron-mode");

/**
 * for AJAX purposes
 */
Route::get('/client_data', function(Request $request){ 
    return Client::find($request->id)->toJson();
});
Route::get('/song_data', function(Request $request){ 
    $song = Song::find($request->id);
    return json_encode(
        array_merge(
            song_quest_type($song->id)->toArray(),
            ["genre" => Genre::find($song->genre_id)->name],
            $song->toArray()
        )
    );
});
Route::post('/price_calc', function(Request $request){ 
    return price_calc($request->labels, $request->price_schema, $request->veteran_discount); 
});
Route::get('/quest_type_from_id', function(Request $request){ 
    return QuestType::where("code", $request->initial)->first()->toJson(); 
});
Route::post('/quest_price_update', function(Request $rq){
    $quest = Quest::findOrFail($rq->id);
    $price_before = $quest->price;
    $quest->update(["price_code_override" => $rq->code, "price" => price_calc($rq->code, $quest->client_id)[0]]);
    app("App\Http\Controllers\BackController")->statusHistory($rq->id, 31, json_encode(["price" => $price_before . " → " . $quest->price]));
});
