<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function input(){
        return view("auth.login", [
            "title" => "Logowanie",
            "forWhom" => "guest"
        ]);
    }

    public function authenticate(Request $request){
        $credentials = $request->validate([
            'login' => ['required'],
            'password' => ['required']
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended("dashboard")->withSuccess("Zalogowano");
        }

        return back()->withErrors("Nieprawidłowe dane logowania");
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("login");
    }
}
