<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function input(){
        return (Auth::check())
            ? redirect()->route("dashboard")
            : view("auth.login", [
                "title" => "Logowanie",
            ]);
    }

    public function authenticate(Request $request){
        $request->validate([
            // 'login' => ['required']
            'password' => ['required']
        ]);

        $credentials = trim($request->password);
        $remember = $request->has("remember");

        $users = User::all();
        foreach($users as $user){
            if($credentials === $user->password){
                Auth::login(User::find($user->id), $remember);
                $request->session()->regenerate();
                return redirect()->intended("dashboard")->with("success", "Zalogowano");
            }
        }

        return back()->with("error", "Nieprawidłowe dane logowania");
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/")->with("success", "Wylogowano");
    }
}
