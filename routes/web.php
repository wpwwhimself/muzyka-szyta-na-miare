<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\SongController;
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

    Route::controller(AuthController::class)->prefix("auth")->group(function(){
        Route::get('/', "input")->name("login");
        Route::post('/back', "authenticate")->name("authenticate");
        Route::post('/register-back', "register")->name("register");
        Route::get('/logout', "logout")->name("logout");
    });

    Route::middleware("auth")->group(function(){
        Route::controller(BackController::class)->group(function(){
            Route::get('/dashboard', "dashboard")->name("dashboard");
            Route::get('/prices', "prices")->name("prices");

            Route::get("/ppp/{page?}", "ppp")->name("ppp");

            Route::get("/settings", "settings")->name("settings");
            Route::post("/settings", "updateSetting")->name("settings-update");

            Route::withoutMiddleware("auth")->get("/patron-mode/{client_id}/{level}", "setPatronLevel")->name("patron-mode");
        });

        Route::controller(RequestController::class)->prefix("requests")->group(function(){
            Route::get('/', "list")->name("requests");
            Route::get('/add', "add")->name("add-request");

            Route::withoutMiddleware("auth")->group(function(){
                Route::get('/view/{id}', "show")->name("request");
                Route::post('/add-back', "processAdd")->name("add-request-back");
                Route::post('/mod-back', "processMod")->name("mod-request-back");

                Route::get('/finalize/{id}/{status}/{with_priority?}', "finalize")->name("request-final");
                Route::get('/finalized/{id}/{status}/{is_new_client}', "finalized")->name("request-finalized");
                Route::post("/finalized-sub", "questReject")->name("quest-reject");
            });
        });

        Route::controller(QuestController::class)->prefix("quests")->group(function(){
            Route::get('/', "list")->name("quests");
            Route::get('/view/{id}', "show")->name("quest");
            Route::get('/add', "addQuest")->name("add-quest");
            Route::post('/mod-back', "processMod")->name("mod-quest-back");

            Route::post("/song-update", "updateSong")->name("quest-song-update");
            Route::post("/quote-update", "updateQuote")->name("quest-quote-update");
            Route::post("/wishes-update", "updateWishes")->name("quest-wishes-update");
            Route::post("/files-ready-update", "updateFilesReady")->name("quest-files-ready-update");
            Route::post("/files-external-update", "updateFilesExternal")->name("quest-files-external-update");
        });

        Route::controller(SongController::class)->prefix("songs")->group(function(){
            Route::get("/", "list")->name("songs");
            Route::get("/edit/{id}", "edit")->name("song-edit");
            Route::post("/process", "process")->name("song-process");
        });

        Route::controller(FileController::class)->group(function(){
            Route::post('/safe-u/{id}', 'fileUpload')->name('upload');
            Route::post('/safe-s', 'fileStore')->name('store');
            Route::get('/safe-d/{id}/{filename}', 'fileDownload')->name('download');
            Route::get('/safe/{id}/{filename}', 'show')->name('safe-show');
            Route::post('/safe/ver-desc-mod', "verDescMod")->name("ver-desc-mod");

            Route::withoutMiddleware("auth")->get("/showcase/show/{id}", "showcaseFileShow")->name("showcase-file-show");
            Route::post("/showcase/upload", "showcaseFileUpload")->name("showcase-file-upload");
        });

        Route::controller(ShowcaseController::class)->prefix("showcases")->group(function(){
            Route::get('/', "list")->name("showcases");
            Route::post('/add', "add")->name("add-showcase");
            Route::post('/add-from-client', "addFromClient")->name("add-client-showcase");
        });

        Route::controller(StatsController::class)->group(function(){
            Route::prefix("stats")->group(function(){
                Route::get("/", "dashboard")->name("stats");
                Route::post("/import", "statsImport")->name("stats-import");

                Route::get("/file-size", "fileSizeReport")->name("file-size-report");

                Route::prefix("quests-calendar")->group(function(){
                    Route::get("/", "questsCalendar")->name("quests-calendar");
                    Route::get("/add-free-day", "qcModFreeDay")->name("qc-mod-free-day");
                });
            });

            Route::prefix("finance")->group(function(){
                Route::get("/", "financeDashboard")->name("finance");
                Route::get("/summary", "financeSummary")->name("finance-summary");
                Route::post("/pay", "financePay")->name("finance-pay");
                Route::get("/return/{quest_id}/{budget?}", "financeReturn")->name("finance-return");

                Route::prefix("invoices")->group(function(){
                    Route::get("/", "invoices")->name("invoices");
                    Route::get("/{id}", "invoice")->name("invoice");
                    Route::post("/visibility", "invoiceVisibility")->name("invoice-visibility");
                    Route::post("/add", "invoiceAdd")->name("invoice-add");
                });

                Route::prefix("costs")->group(function(){
                    Route::get("/", "costs")->name("costs");
                    Route::post("/mod", "modCost")->name("mod-cost");
                    Route::get("/types", "costTypes")->name("cost-types");
                    Route::post("/types/mod", "modCostType")->name("mod-cost-type");
                });

                Route::get("/taxes", "taxes")->name("taxes");
            });
        });

        Route::controller(ClientController::class)->prefix("clients")->group(function(){
            Route::get('list/{param?}/{value?}', "list")->name("clients");
            Route::get("view/{id}", "view")->name("client-view");
            Route::post("edit/{id}", "edit")->name("client-edit");
        });

        Route::controller(WorkClockController::class)->prefix("studio-view")->group(function() {
            Route::get("/", "main")->name("studio");
            Route::get("/{quest_id}", "index")->name("studio-view");
        });

        Route::controller(SpellbookController::class)->middleware("cancastspells")->group(function(){
            Route::prefix("requests")->group(function(){
                Route::get('/view/{id}/obliterate', "obliterate");
                Route::get("/view/{id}/silence", "silence");
                Route::get("/view/{id}/transmute/{property}/{value?}", "transmute");
                Route::get("/view/{id}/reprice/{new_code}", "reprice");
            });

            Route::prefix("quests")->group(function(){
                Route::get("/view/{id}/restatus/{status_id}", "restatus");
                Route::get("/view/{id}/silence", "silence");
                Route::get("/view/{id}/transmute/{property}/{value?}", "transmute");
                Route::get("/view/{id}/polymorph/{letter}", "polymorph");
                Route::get("/view/{id}/reprice/{new_code}", "reprice");
            });
        });
    });

    /* MAILING */
    Route::get("/mp/{method}/{params}", function($method, $params = null){
        $method = "App\\Mail\\".$method;
        $params = explode(",", $params);
        return new $method(...$params);
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
