<?php

use App\Http\Controllers\PatchController;
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
    Route::get("modes", "modes");
    Route::get("song-data-by-quest/{quest_id}", "songDataByQuest");
    Route::get("logs", "logDetails");
    Route::post('start-stop', "startStop");
    Route::get('remove/{song_id}/{status_id}', "remove");
});
