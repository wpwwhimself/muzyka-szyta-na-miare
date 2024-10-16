<?php

use App\Http\Controllers\JanitorController;
use App\Http\Controllers\PatchController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WorkClockController;
use App\Models\Client;
use App\Models\QuestType;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

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
Route::get("/get_ver_desc", function(Request $rq){
    return Storage::get($rq->path) ?? "";
});

Route::post("/settings_change", function(Request $rq){
    if(Auth::id() != 1) return;
    DB::table("settings")
        ->where("setting_name", $rq->setting_name)
        ->update(["value_str" => $rq->value_str])
    ;
});

Route::controller(PatchController::class)->group(function(){
    Route::prefix("quests")->group(function(){
        Route::patch("/{id}/{mode?}", "patchQuest");
    });

    Route::prefix("songs")->group(function(){
        Route::patch("/{id}/{mode?}", "patchSong");
    });
});

Route::controller(StatsController::class)->group(function() {
    Route::prefix("invoice")->group(function() {
        Route::get("/{id}", "invoice");
    });
});

Route::controller(WorkClockController::class)->prefix("clock")->group(function() {
    Route::get("active-quests", "activeQuests");
    Route::get("modes", "modes");
    Route::get("song-data-by-quest/{quest_id}", "songDataByQuest");
    Route::get("logs", "logDetails");
    Route::post('start-stop', "startStop");
    Route::get('remove/{song_id}/{status_id}', "remove");
});

Route::controller(JanitorController::class)->group(function(){
    Route::get("/janitor", "index"); //TODO write as job
});
