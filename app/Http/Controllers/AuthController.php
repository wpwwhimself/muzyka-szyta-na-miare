<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function input(){
        return view("login", [
            "title" => "Logowanie",
            "forWhom" => "guest"
        ]);
    }
}
