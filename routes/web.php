<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JanitorController;
use App\Http\Controllers\StatsController;
use App\Models\Client;
use App\Models\Genre;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\Request as ModelsRequest;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    Route::get('/requests/view/{id}', "request")->name("request");
    Route::post('/requests/add-back', "addRequestBack")->name("add-request-back");
    Route::post('/requests/mod-back', "modRequestBack")->name("mod-request-back");

    Route::get('/requests/finalize/{id}/{status}', "requestFinal")->name("request-final");
    Route::post("/request-finalized-sub", "questReject")->name("quest-reject");

    Route::middleware("auth")->group(function(){
        Route::get('/dashboard', "dashboard")->name("dashboard");
        Route::get('/prices', "prices")->name("prices");

        Route::get('/requests', "requests")->name("requests");
        Route::get('/requests/add', "addRequest")->name("add-request");

        Route::get('/quests/{client_id?}', "quests")->name("quests");
        Route::get('/quests/view/{id}', "quest")->name("quest");
        Route::get('/quests/add', "addQuest")->name("add-quest");
        Route::post('/quests/mod-back', "modQuestBack")->name("mod-quest-back");
        Route::post('/quests/work-clock', "workClock")->name("work-clock");
        Route::get('/quests/work-clock-remove/{song_id}/{status_id}', "workClockRemove")->name("work-clock-remove");
        Route::post("/quest-song-update", "questSongUpdate")->name("quest-song-update");
        Route::post("/quest-quote-update", "questQuoteUpdate")->name("quest-quote-update");
        Route::post("/quest-wishes-update", "questWishesUpdate")->name("quest-wishes-update");

        Route::get('/clients/{param?}/{value?}', "clients")->name("clients");

        Route::get('/showcases', "showcases")->name("showcases");
        Route::post('/showcases/add', "addShowcase")->name("add-showcase");

        Route::get("/songs", "songs")->name("songs");

        Route::get("/ppp", "ppp")->name("ppp");

        Route::get("/settings", "settings")->name("settings");
    });
});

Route::controller(FileController::class)->group(function(){
    Route::middleware("auth")->group(function(){
        Route::post('/safe-u/{id}', 'fileUpload')->name('upload');
        Route::post('/safe-s', 'fileStore')->name('store');
        Route::get('/safe-d/{id}/{filename}', 'fileDownload')->name('download');
        Route::get('/safe/{id}/{filename}', 'show')->name('safe-show');
        Route::post('/safe/ver-desc-mod', "verDescMod")->name("ver-desc-mod");
    });
});

Route::controller(StatsController::class)->group(function(){
    Route::middleware("auth")->group(function(){
        Route::get("/stats", "dashboard")->name("stats");
        Route::post("/stats-import", "statsImport")->name("stats-import");
        Route::get("/finance", "financeDashboard")->name("finance");
        Route::post("/finance/pay", "financePay")->name("finance-pay");

        Route::get("/finance/invoice/{id}", "invoice")->name("invoice");
        Route::post("/finance/invoice/visibility", "invoiceVisibility")->name("invoice-visibility");
        Route::post("/finance/invoice/add", "invoiceAdd")->name("invoice-add");

        Route::get("/finance/costs", "costs")->name("costs");
        Route::post("/finance/costs/mod", "modCost")->name("mod-cost");
        Route::get("/finance/costs/types", "costTypes")->name("cost-types");
        Route::post("/finance/costs/types/mod", "modCostType")->name("mod-cost-type");

        Route::get("/stats/file-size", "fileSizeReport")->name("file-size-report");
    });
});

Route::get('/request-finalized/{id}/{status}/{is_new_client}', function($id, $status, $is_new_client){
    return view("request-finalized", array_merge(
        ["title" => "Zapytanie zamknięte"],
        compact("id", "status", "is_new_client")
    ));
})->name("request-finalized");
Route::get("/patron-mode/{id}/{level}", function($id, $level){
    Client::findOrFail($id)->update(["helped_showcasing" => $level]);
    if(Auth::id() == 1) return redirect()->route("dashboard")->with("success", ($level == 2) ? "Wniosek przyjęty" : "Wniosek odrzucony");
    return redirect()->route("dashboard")->with("success", "Wystawienie opinii odnotowane");
})->name("patron-mode");

/* MAILING */
Route::get("/mp-rq/{id}", function($id){ return new App\Mail\RequestQuoted(ModelsRequest::findOrFail($id)); })->name("mp-rq");
Route::get("/mp-q/{id}", function($id){ return new App\Mail\QuestUpdated(Quest::findOrFail($id)); })->name("mp-q");
Route::get("/mp-q-p/{id}", function($id){ return new App\Mail\PaymentReceived(Quest::findOrFail($id)); })->name("mp-q-p");
Route::get("/mp-w/{id}", function($id){ return new App\Mail\_Welcome(Client::findOrFail($id)); })->name("mp-w");
Route::get("/mp-aqm/{id}", function($id){ return new App\Mail\ArchmageQuestMod(Quest::findOrFail($id)); })->name("mp-aqm");

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
            ["type" => song_quest_type($song->id)->type],
            ["genre" => Genre::find($song->genre_id)->name],
            $song->toArray()
        )
    );
});
Route::post('/price_calc', function(Request $request){
    return price_calc($request->labels, $request->price_schema, $request->quoting);
});
Route::get('/quest_type_from_id', function(Request $request){
    return QuestType::where("code", $request->initial)->first()->toJson();
});
Route::post('/budget_update', function(Request $rq){
    $client = Client::findOrFail($rq->client_id);
    $client->update(["budget" => $rq->new_budget]);
});
Route::get("/get_ver_desc", function(Request $rq){
    return Storage::get($rq->path) ?? "";
});
Route::get("/songs_info", function(Request $rq){
    return Song::orderByRaw("ISNULL(title)")
        ->orderBy("title")
        ->orderBy("artist")
        ->select(["title", "artist"])
        ->distinct()
        ->get();
});

Route::controller(JanitorController::class)->group(function(){
    Route::get("/re_quests_janitor", "index");
});

Route::post("/settings_change", function(Request $rq){
    DB::table("settings")
        ->where("setting_name", $rq->setting_name)
        ->update(["value_str" => $rq->value_str])
    ;
});


/*
Route::get("/greenlight", function(Request $rq){
    $clients_with_email = Client::whereNotNull("email")->get();
    $output = "";

    if($rq->ready != 1){
        $tyle = $clients_with_email->count();
        $output = <<<EOS
<p>Za chwilę oficjalnie ruszy nowa strona i powiadomimy $tyle ludzi o fakcie, że mają u mnie konto. Chcesz kontynuować?</p>
<a href="?ready=1">TAK!</a>
EOS;
    }else{
        $output = <<<EOS
<p>No to jazda!</p>
<ol>
EOS;
        foreach($clients_with_email as $client){
            $output .= "<li>Mail do: $client->client_name ($client->email)</li>";
            Mail::to($client->email)->send(new _Welcome($client));
        }

        $output .= <<<EOS
</ol>
<p>Dobra robota!</p>
EOS;
    }

    return $output;
})->middleware("auth");
*/
