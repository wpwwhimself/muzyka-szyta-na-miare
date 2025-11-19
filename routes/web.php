<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DjController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SpellbookController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WorkClockController;
use App\Http\Middleware\Shipyard\EnsureUserHasRole;
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

if (file_exists(__DIR__.'/Shipyard/shipyard.php')) require __DIR__.'/Shipyard/shipyard.php';

Route::get('/', [HomeController::class, "index"])->name("home");

Route::redirect("/profile", "/dashboard");

Route::middleware("auth")->group(function(){
    Route::controller(BackController::class)->group(function(){
        Route::get('/dashboard', "dashboard")->name("dashboard");
        Route::get('/prices', "prices")->name("prices");

        Route::withoutMiddleware("auth")->get("/patron-mode/{client_id}/{level}", "setPatronLevel")->name("patron-mode");
        Route::withoutMiddleware("auth")->post("restatus-with-comment", "restatusReQuestWithComment")->name("re_quests.restatus-with-comment");

        Route::prefix("lookup")->group(function() {
            Route::get("users", "lookupUsers")->name("lookup.users");
            Route::get("songs", "lookupSongs")->name("lookup.songs");
        });
    });

    Route::controller(RequestController::class)->prefix("requests")->group(function(){
        Route::get('/', "list")->name("requests");
        Route::get('/add', "add")->name("add-request");

        Route::withoutMiddleware("auth")->group(function(){
            Route::prefix("new")->group(function(){
                Route::post("/podklady", "newRequestPodklady")->name("requests.new");
                Route::post("/organista", "newRequestOrganista")->name("organ-requests.new");
                Route::post("/dj", "newRequestDj")->name("dj-requests.new");
            });

            Route::get('/view/{id}', "show")->name("request");
            Route::post('/add-back', "processAdd")->name("add-request-back");
            Route::post('/mod-back', "processMod")->name("mod-request-back");

            Route::prefix("select")->group(function() {
                Route::post("user", "selectUser")->name("requests.select-user");
                Route::post("song", "selectSong")->name("requests.select-song");
            });

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
        Route::prefix("genres")->group(function(){
            Route::get("/list", "listGenres")->name("song-genres");
            Route::get("/edit/{id?}", "editGenre")->name("song-genre-edit");
            Route::post("/process", "processGenre")->name("song-genre-process");
        });

        Route::prefix("tags")->group(function(){
            Route::get("/list", "listTags")->name("song-tags");
            Route::get("/edit/{id?}", "editTag")->name("song-tag-edit");
            Route::post("/process", "processTag")->name("song-tag-process");
        });

        Route::get("/list", "list")->name("songs");
        Route::get("/edit/{id}", "edit")->name("song-edit");
        Route::post("/process", "process")->name("song-process");

        Route::get("downloader", "downloader")->name("song-downloader");
    });

    Route::controller(FileController::class)->group(function(){
        Route::prefix("files")->group(function(){
            Route::get("/upload/{entity_name}/{id}", "uploadByEntity")->name("files-upload-by-entity");
            Route::get("/edit/{id}", "edit")->name("files-edit");
            Route::post("/process", "process")->name("files-process");
            Route::get("/addFromExistingSafe/{song_id}", "addFromExisingSafe")->name("files-add-from-existing-safe");
            Route::post("/addFromExistingSafe", "addFromExistingSafeProcess")->name("files-process-add-from-existing-safe");

            Route::prefix("tags")->group(function(){
                Route::get("/list", "listTags")->name("file-tags");
                Route::get("/edit/{id?}", "editTag")->name("file-tag-edit");
                Route::post("/process", "processTag")->name("file-tag-process");
            });
        });

        Route::post('/safe-u/{id}', 'fileUpload')->name('upload');
        Route::post('/safe-s', 'fileStore')->name('store');
        Route::get('/safe-d/{id}/{filename}', 'fileDownload')->name('download');
        Route::get('/safe/{id}/{filename}', 'show')->name('safe-show');
        Route::post('/safe/ver-desc-mod', "verDescMod")->name("ver-desc-mod");

        Route::withoutMiddleware("auth")
            ->get("/showcase/show/{id}", "showcaseFileShow")->name("showcase-file-show");
        Route::withoutMiddleware("auth")->domain(implode(".", [env("DJ_SUBDOMAIN"), env("APP_DOMAIN")]))
            ->get("/showcase/show/{id}", "showcaseFileShow")->name("showcase-file-show");
        Route::post("/showcase/upload", "showcaseFileUpload")->name("showcase-file-upload");
    });

    Route::controller(ShowcaseController::class)->prefix("showcases")->group(function(){
        Route::get('/', "list")->name("showcases");
        Route::post('/add', "add")->name("add-showcase");
        Route::post('/add-from-client', "addFromClient")->name("add-client-showcase");
        Route::get("/pin-comment/{comment_id}/{client_id}", "pinComment")->name("showcase-pin-comment");

        Route::prefix("organ")->group(function() {
            Route::get("/edit/{showcase?}", "editOrgan")->name("organ-showcase-edit");
            Route::post("/edit", "processOrgan")->name("organ-showcase-process");
        });
        Route::prefix("dj")->group(function() {
            Route::get("/edit/{showcase?}", "editDj")->name("dj-showcase-edit");
            Route::post("/edit", "processDj")->name("dj-showcase-process");
        });

        Route::prefix("platforms")->group(function() {
            Route::get("/", "listPlatforms")->name("showcase-platforms");
            Route::get("/edit/{id?}", "editPlatform")->name("showcase-platform-edit");
            Route::post("/process", "processPlatform")->name("showcase-platform-process");
        });
    });

    Route::controller(StatsController::class)->group(function(){
        Route::prefix("stats")->group(function(){
            Route::get("/", "dashboard")->name("stats");
            Route::get("/gigs", "gigsDashboard")->name("stats-gigs");
            Route::post("/import", "statsImport")->name("stats-import");

            Route::get("/file-size", "fileSizeReport")->name("file-size-report");

            Route::prefix("quests-calendar")->group(function(){
                Route::get("/", "questsCalendar")->name("quests-calendar");
                Route::get("/add-free-day", "qcModFreeDay")->name("qc-mod-free-day");
            });

            Route::post("/gig-transaction/add", "addGigTransaction")->name("gig-transaction.add");
        });

        Route::prefix("finance")->group(function(){
            Route::get("/", "financeDashboard")->name("finance");
            Route::get("/summary", "financeSummary")->name("finance-summary");
            Route::post("/pay", "financePay")->name("finance-pay");
            Route::get("/payout/{amount}", "financePayout")->name("finance-payout");
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

            Route::prefix("gig-price")->group(function(){
                Route::get("/suggest", "gigPriceSuggest")->name("gig-price-suggest");
            });
        });
    });

    Route::controller(ClientController::class)->prefix("clients")->group(function(){
        Route::get('list/{param?}/{value?}', "list")->name("clients");
        Route::get("view/{id}", "view")->name("client-view");
        Route::post("edit/{id}", "edit")->name("client-edit");

        Route::prefix("mail")->group(function(){
            Route::get("/{client_id?}", "mailPrepare")->name("client-mail-prepare");
            Route::post("/send", "mailSend")->name("client-mail-send");
        });
    });

    Route::controller(WorkClockController::class)->prefix("studio-view")->group(function() {
        Route::get("/", "main")->name("studio");
        Route::get("/{quest_id}", "index")->name("studio-view");
    });

    Route::controller(DjController::class)->middleware("cancastspells")->prefix("dj")->group(function () {
        Route::get("/", "index")->name("dj");

        Route::get("gig", "gigMode")->name("dj-gig-mode");

        Route::prefix("songs")->group(function () {
            Route::get("list", "listSongs")->name("dj-list-songs");
            Route::get("edit/{id?}", "editSong")->name("dj-edit-song");
            Route::post("edit", "processSong")->name("dj-process-song");
        });

        Route::prefix("sample-sets")->group(function () {
            Route::get("list", "listSampleSets")->name("dj-list-sample-sets");
            Route::get("edit/{id?}", "editSampleSet")->name("dj-edit-sample-set");
            Route::post("edit", "processSampleSet")->name("dj-process-sample-set");
        });

        Route::prefix("sets")->group(function () {
            Route::get("list", "listSets")->name("dj-list-sets");
            Route::get("edit/{id?}", "editSet")->name("dj-edit-set");
            Route::post("edit", "processSet")->name("dj-process-set");
        });
    });

    Route::controller(SpellbookController::class)->middleware(EnsureUserHasRole::class.":spellcaster")->group(function () {
        foreach (SpellbookController::SPELLS as $spell => $routes) {
            foreach ($routes as $route) {
                Route::get($route, $spell);
            }
        }
    });
});

/* MAILING */
Route::get("/mp/{method}/{params}", function($method, $params = null){
    $method = "App\\Mail\\".$method;
    $params = explode(",", $params);
    return new $method(...$params);
});

// Route::domain(implode(".", [env("ORGANISTA_SUBDOMAIN"), env("APP_DOMAIN")]))->group(function(){
//     Route::get('/', [HomeController::class, "organista"])->name("home-organista");
// });

// Route::domain(implode(".", [env("DJ_SUBDOMAIN"), env("APP_DOMAIN")]))->group(function(){
//     Route::get('/', [HomeController::class, "dj"])->name("home-dj");
// });

/*
Route::get("/greenlight", function(Request $rq){
    $clients_with_email = User::whereNotNull("email")->get();
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
