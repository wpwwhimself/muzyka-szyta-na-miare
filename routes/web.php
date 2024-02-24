<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JanitorController;
use App\Http\Controllers\SpellbookController;
use App\Http\Controllers\StatsController;
use App\Models\Client;
use App\Models\QuestType;
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

    Route::get('/requests/finalize/{id}/{status}/{with_priority?}', "requestFinal")->name("request-final");
    Route::get('/request-finalized/{id}/{status}/{is_new_client}', "requestFinalized")->name("request-finalized");
    Route::post("/request-finalized-sub", "questReject")->name("quest-reject");

    Route::middleware("auth")->group(function(){
        Route::get('/dashboard', "dashboard")->name("dashboard");
        Route::get('/prices', "prices")->name("prices");

        Route::get('/requests', "requests")->name("requests");
        Route::get('/requests/add', "addRequest")->name("add-request");

        Route::prefix("quests")->group(function(){
            Route::get('/', "quests")->name("quests");
            Route::get('/view/{id}', "quest")->name("quest");
            Route::get('/add', "addQuest")->name("add-quest");
            Route::post('/mod-back', "modQuestBack")->name("mod-quest-back");
            Route::post('/work-clock', "workClock")->name("work-clock");
            Route::get('/work-clock-remove/{song_id}/{status_id}', "workClockRemove")->name("work-clock-remove");
        });
        Route::post("/quest-song-update", "questSongUpdate")->name("quest-song-update");
        Route::post("/quest-quote-update", "questQuoteUpdate")->name("quest-quote-update");
        Route::post("/quest-wishes-update", "questWishesUpdate")->name("quest-wishes-update");
        Route::post("/quest-files-ready-update", "questFilesReadyUpdate")->name("quest-files-ready-update");

        Route::get('/showcases', "showcases")->name("showcases");
        Route::post('/showcases/add', "addShowcase")->name("add-showcase");

        Route::get("/songs", "songs")->name("songs");

        Route::get("/ppp/{page?}", "ppp")->name("ppp");

        Route::get("/settings", "settings")->name("settings");
    });

    Route::get("/patron-mode/{client_id}/{level}", "setPatronLevel")->name("patron-mode");
});

Route::controller(FileController::class)->group(function(){
    Route::get("/showcase/show/{id}", "showcaseFileShow")->name("showcase-file-show");

    Route::middleware("auth")->group(function(){
        Route::post('/safe-u/{id}', 'fileUpload')->name('upload');
        Route::post('/safe-s', 'fileStore')->name('store');
        Route::get('/safe-d/{id}/{filename}', 'fileDownload')->name('download');
        Route::get('/safe/{id}/{filename}', 'show')->name('safe-show');
        Route::post('/safe/ver-desc-mod', "verDescMod")->name("ver-desc-mod");

        Route::post("/showcase/upload", "showcaseFileUpload")->name("showcase-file-upload");
    });
});

Route::controller(StatsController::class)->group(function(){
    Route::middleware("auth")->group(function(){
        Route::get("/stats", "dashboard")->name("stats");
        Route::post("/stats-import", "statsImport")->name("stats-import");

        Route::prefix("finance")->group(function(){
            Route::get("/", "financeDashboard")->name("finance");
            Route::get("/summary", "financeSummary")->name("finance-summary");
            Route::post("/pay", "financePay")->name("finance-pay");

            Route::get("/invoices", "invoices")->name("invoices");
            Route::get("/invoices/{id}", "invoice")->name("invoice");
            Route::post("/invoices/visibility", "invoiceVisibility")->name("invoice-visibility");
            Route::post("/invoices/add", "invoiceAdd")->name("invoice-add");

            Route::get("/costs", "costs")->name("costs");
            Route::post("/costs/mod", "modCost")->name("mod-cost");
            Route::get("/costs/types", "costTypes")->name("cost-types");
            Route::post("/costs/types/mod", "modCostType")->name("mod-cost-type");

            Route::get("/taxes", "taxes")->name("taxes");
        });

        Route::get("/stats/file-size", "fileSizeReport")->name("file-size-report");

        Route::get("/stats/quests-calendar", "questsCalendar")->name("quests-calendar");
        Route::get("/stats/quests-calendar/add-free-day", "qcModFreeDay")->name("qc-mod-free-day");
    });
});

Route::controller(ClientController::class)->group(function(){
    Route::middleware("auth")->group(function(){
        Route::prefix("clients")->group(function(){
            Route::get('list/{param?}/{value?}', "list")->name("clients");
            Route::get("view/{id}", "view")->name("client-view");
            Route::post("edit/{id}", "edit")->name("client-edit");
        });
    });
});

/* MAILING */
Route::get("/mp/{method}/{params}", function($method, $params = null){
    $method = "App\\Mail\\".$method;
    $params = explode(",", $params);
    return new $method(...$params);
});

Route::controller(SpellbookController::class)->middleware(["auth", "cancastspells"])->group(function(){
    Route::prefix("requests")->group(function(){
        Route::get('/view/{id}/obliterate', "obliterate");
        Route::get("/view/{id}/silence", "silence");
        Route::get("/view/{id}/transmute/{property}/{value?}", "transmute");
    });

    Route::prefix("quests")->group(function(){
        Route::get("/view/{id}/restatus/{status_id}", "restatus");
        Route::get("/view/{id}/silence", "silence");
        Route::get("/view/{id}/transmute/{property}/{value?}", "transmute");
        Route::get("/view/{id}/polymorph/{letter}", "polymorph");
    });
});

/**
 * for AJAX purposes
 */
// front -- listing songs
Route::get("/songs_info", function(Request $rq){
    $songs = Song::orderByRaw("ISNULL(title)")
        ->where("id", "not like", "O%")
        ->orderBy("title")
        ->orderBy("artist")
        ->select(["id", "title", "artist"])
        ->distinct()
        ->get();

    return $songs;
});

Route::get('/client_data', function(Request $request){
    $data = Client::find($request->id)->toArray();
    foreach($data as $key => $value){
        if(!preg_match("/id/", $key)) $data[$key] = _ct_($value);
    }
    return json_encode($data);
});
Route::get('/song_data', function(Request $request){
    return Song::find($request->id)->toJson();
});
Route::post('/song_link_change', function(Request $request){
    if(Auth::id() != 1) return;
    $id = $request->id;
    Song::find($id)->update(["link" => $request->link]);
});
Route::post('/price_calc', function(Request $request){
    return price_calc($request->labels, $request->price_schema, $request->quoting);
});
Route::post('/monthly_payment_limit', function(Request $request){
    return app("App\Http\Controllers\StatsController")->monthlyPaymentLimit($request->amount);
});
Route::get('/quest_type_from_id', function(Request $request){
    return QuestType::where("code", $request->initial)->first()->toJson();
});
Route::get("/get_ver_desc", function(Request $rq){
    return Storage::get($rq->path) ?? "";
});

Route::controller(JanitorController::class)->group(function(){
    Route::get("/janitor", "index");
});

Route::post("/settings_change", function(Request $rq){
    if(Auth::id() != 1) return;
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
