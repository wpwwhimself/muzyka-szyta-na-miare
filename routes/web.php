<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SpellbookController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WorkClockController;
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

$domain = env("APP_DOMAIN" ?? "muzykaszytanamiare.pl");

Route::domain("podklady.".$domain)->group(function(){
    Route::get('/', [HomeController::class, "podklady"])->name("home-podklady");

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
            });
            Route::post("/quest-song-update", "questSongUpdate")->name("quest-song-update");
            Route::post("/quest-quote-update", "questQuoteUpdate")->name("quest-quote-update");
            Route::post("/quest-wishes-update", "questWishesUpdate")->name("quest-wishes-update");
            Route::post("/quest-files-ready-update", "questFilesReadyUpdate")->name("quest-files-ready-update");
            Route::post("/quest-files-external-update", "questFilesExternalUpdate")->name("quest-files-external-update");

            Route::get('/showcases', "showcases")->name("showcases");
            Route::post('/showcases/add', "addShowcase")->name("add-showcase");
            Route::post('/showcases/add-from-client', "addShowcaseFromClient")->name("add-client-showcase");

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
                Route::get("/return/{quest_id}/{budget?}", "financeReturn")->name("finance-return");

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

    Route::controller(WorkClockController::class)->middleware("auth")->prefix("studio-view")->group(function() {
        Route::get("/", "main")->name("studio");
        Route::get("/{quest_id}", "index")->name("studio-view");
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
});

Route::domain("organista.".$domain)->group(function(){
    Route::get('/', [HomeController::class, "organista"])->name("home-organista");
});

Route::domain("dj.".$domain)->group(function(){
    Route::get('/', [HomeController::class, "dj"])->name("home-dj");
});

Route::domain($domain)->group(function(){
    Route::get('/', [HomeController::class, "index"])->name("home");
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
