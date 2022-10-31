<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\HomeController;
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
Route::get('/auth', [AuthController::class, "input"])->name("login");
Route::post('/auth-back', [AuthController::class, "authenticate"])->name("authenticate");
Route::post('/auth/register-back', [AuthController::class, "register"])->name("register");
Route::get('/auth/logout', [AuthController::class, "logout"])->name("logout");
Route::get('/dashboard', [BackController::class, "dashboard"])->middleware("auth")->name("dashboard");

Route::get('/quests', [BackController::class, "quests"])->middleware("auth")->name("quests");
Route::get('/quests/view/{id}', [BackController::class, "quest"])->middleware("auth")->name("quest");
Route::get('/quests/add', [BackController::class, "addQuest"])->middleware("auth")->name("add-quest");
Route::post('/quests/add-back', [BackController::class, "addQuestBack"])->middleware("auth")->name("add-quest-back");
Route::post('/quests/mod-back', [BackController::class, "modQuestBack"])->middleware("auth")->name("mod-quest-back");

Route::get('/requests', [BackController::class, "requests"])->middleware("auth")->name("requests");
Route::get('/requests/view/{id}', [BackController::class, "request"])->middleware("auth")->name("request");
Route::get('/requests/add', [BackController::class, "addRequest"])->middleware("auth")->name("add-request");
Route::post('/requests/add-back', [BackController::class, "addRequestBack"])->middleware("auth")->name("add-request-back");
Route::post('/requests/mod-back', [BackController::class, "modRequestBack"])->middleware("auth")->name("mod-request-back");

Route::get('/clients', [BackController::class, "clients"])->middleware("auth")->name("clients");
Route::get('/clients/view/{id}', [BackController::class, "client"])->middleware("auth")->name("client");

Route::get('/ads', [BackController::class, "ads"])->middleware("auth")->name("ads");
Route::get('/messages', [BackController::class, "messages"])->middleware("auth")->name("messages");
