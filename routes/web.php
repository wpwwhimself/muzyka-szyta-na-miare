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
Route::post('/auth/login', [AuthController::class, "authenticate"])->name("authenticate");
// Route::get('/auth/manual-new-user', function(){
//     return view("auth.createnew");
// });
Route::post('/auth/register', [AuthController::class, "register"])->name("register");
Route::get('/auth/logout', [AuthController::class, "logout"])->name("logout");

Route::get('/dashboard', [BackController::class, "dashboard"])->middleware("auth")->name("dashboard");
Route::get('/quests', [BackController::class, "quests"])->middleware("auth")->name("quests");
Route::get('/quests/q{id}', [BackController::class, "quest"])->middleware("auth")->name("quest");
Route::get('/quests/rq{id}', [BackController::class, "request"])->middleware("auth")->name("request");

Route::get('/clients', [BackController::class, "clients"])->middleware("auth")->name("clients");
Route::get('/clients/{id}', [BackController::class, "client"])->middleware("auth")->name("client");
Route::get('/ads', [BackController::class, "ads"])->middleware("auth")->name("ads");
Route::get('/messages', [BackController::class, "messages"])->middleware("auth")->name("messages");
