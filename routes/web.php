<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Mail\QuestUpdated;
use App\Models\Client;
use App\Models\Genre;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request as ModelsRequest;
use App\Models\Song;
use App\Models\StatusChange;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
    Route::get('/prices', "prices")->middleware("auth")->name("prices");

    Route::get('/requests', "requests")->middleware("auth")->name("requests");
    Route::get('/requests/view/{id}', "request")->name("request");
    Route::get('/requests/add', "addRequest")->middleware("auth")->name("add-request");
    Route::post('/requests/mod-back', "modRequestBack")->name("mod-request-back");

    Route::get('/quests', "quests")->middleware("auth")->name("quests");
    Route::get('/quests/view/{id}', "quest")->middleware("auth")->name("quest");
    Route::get('/quests/add', "addQuest")->middleware("auth")->name("add-quest");
    Route::post('/quests/mod-back', "modQuestBack")->middleware("auth")->name("mod-quest-back");
    Route::post('/quests/work-clock', "workClock")->middleware("auth")->name("work-clock");

    Route::get('/requests/finalize/{id}/{status}', "requestFinal")->name("request-final");
    Route::post("/request-finalized-sub", "questReject")->name("quest-reject");

    Route::get('/clients', "clients")->middleware("auth")->name("clients");
    Route::get('/clients/view/{id}', "client")->middleware("auth")->name("client");

    Route::get('/showcases', "showcases")->middleware("auth")->name("showcases");
    Route::get("/songs", "songs")->middleware("auth")->name("songs");
});
Route::controller(FileController::class)->group(function(){
    Route::post('/safe-u', 'fileUpload')->middleware("auth")->name('upload');
    Route::get('/safe-d/{id}/{filename}', 'fileDownload')->middleware("auth")->name('download');
    Route::get('/safe/{id}/{filename}', 'show')->middleware("auth")->name('safe-show');
    Route::post('/safe/ver-desc-mod', "verDescMod")->middleware("auth")->name("ver-desc-mod");
});

Route::get('/request-finalized/{id}/{status}/{is_new_client}', function($id, $status, $is_new_client){
    return view("request-finalized", array_merge(
        ["title" => "Zapytanie zamknięte"],
        compact("id", "status", "is_new_client")
    ));
})->name("request-finalized");
Route::get("/patron-mode/{id}/{level}", function($id, $level){
    Client::findOrFail($id)->update(["helped_showcasing" => $level]);
    if(Auth::id() == 1) return redirect()->route("dashboard")->with("success", "Klient jest teraz patronem");
    return redirect()->route("dashboard")->with("success", "Wystawienie opinii odnotowane");
})->name("patron-mode");


Route::get("/mp-rq/{id}", function($id){ return new App\Mail\RequestQuoted(ModelsRequest::findOrFail($id)); })->name("mp-rq");
Route::get("/mp-q/{id}", function($id){ return new App\Mail\QuestUpdated(Quest::findOrFail($id)); })->name("mp-q");
Route::get("/mp-q-p/{id}", function($id){ return new App\Mail\PaymentReceived(Quest::findOrFail($id)); })->name("mp-q-p");

/**
 * for AJAX purposes
 */
Route::get('/client_data', function(Request $request){
    return Client::find($request->id)->toJson();
});
Route::get('/song_data', function(Request $request){
    $song = Song::findOrFail($request->id);
    return json_encode(
        array_merge(
            ["type" => song_quest_type($song->id)],
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
    $quest->update([
        "price_code_override" => $rq->code,
        "price" => price_calc($rq->code, $quest->client_id)[0],
        "paid" => ($quest->payments->sum("comment") >= price_calc($rq->code, $quest->client_id)[0])
    ]);

    // sending mail
    $mailing = null;
    if($quest->client->email){
        Mail::to($quest->client->email)->send(new QuestUpdated($quest));
        $mailing = true;
    }else{
        $mailing = false;
    }
    app("App\Http\Controllers\BackController")->statusHistory(
        $rq->id,
        31,
        json_encode(["price" => $price_before . " → " . $quest->price]),
        null,
        $mailing
    );
});
Route::post('/budget_update', function(Request $rq){
    $client = Client::findOrFail($rq->client_id);
    $client->update(["budget" => $rq->new_budget]);
});
Route::get("/get_ver_desc", function(Request $rq){
    return Storage::get($rq->path) ?? "";
});
