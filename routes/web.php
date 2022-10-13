<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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
Route::get('/auth/createnew', function(){
    return view("auth.createnew");
});
Route::post('/auth/register', [AuthController::class, "register"])->name("register");
Route::get('/auth/logout', [AuthController::class, "logout"])->name("logout");
Route::get('/dashboard', [HomeController::class, "dashboard"])->middleware("auth")->name("dashboard");
