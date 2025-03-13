<?php

use App\Http\Controllers\BackController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DjController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\JanitorController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WorkClockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(QuestController::class)->prefix("quests")->group(function() {
    Route::patch("/{id}/{mode?}", "patch");
});

Route::controller(SongController::class)->prefix("songs")->group(function() {
    Route::get("/tags", "getTags");
    Route::get("/info", "getForFront");
    Route::post('/change-link', "changeLink");
    Route::patch("/{id}/{mode?}", "patch");
    Route::get('/{id}', "getById");
});

Route::controller(ClientController::class)->prefix("clients")->group(function() {
    Route::get('/{id}', "getById");
});

Route::controller(StatsController::class)->group(function() {
    Route::post('/price_calc', "priceCalc");
    Route::post('/monthly_payment_limit', "monthlyPaymentLimit");

    Route::prefix("invoice")->group(function() {
        Route::get("/{id}", "invoice");
    });
});

Route::controller(FileController::class)->group(function() {
    Route::get("/get_ver_desc/{file_id}", "verDescGet");
});

Route::controller(WorkClockController::class)->prefix("clock")->group(function() {
    Route::get("active-quests", "activeQuests");
    Route::get("modes", "modes");
    Route::get("song-data-by-quest/{quest_id}", "songDataByQuest");
    Route::get("logs", "logDetails");
    Route::post('start-stop', "startStop");
    Route::get('remove/{song_id}/{status_id}', "remove");
});

Route::controller(DjController::class)->prefix("dj")->group(function() {
    Route::prefix("gig-mode")->group(function() {
        Route::get("init-data", "gigModeInit");
        Route::get("song/{id}", "gigModeSong");
        Route::get("set/{id}", "gigModeSet");
    });
});
