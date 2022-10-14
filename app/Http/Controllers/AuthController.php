<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function input(){
        return view("auth.login", [
            "title" => "Logowanie",
            "forWhom" => "guest"
        ]);
    }

    public function authenticate(Request $request){
        $request->validate([
            'login' => ['required'],
            'password' => ['required']
        ]);

        $credentials = $request->only('login', 'password');
        $remember = $request->input('remember') == "on";
        if(Auth::attempt($credentials, $remember)){
            $request->session()->regenerate();
            return redirect()->intended("dashboard")->with("status", "Zalogowano");
        }

        return back()->with("status", "Nieprawidłowe dane logowania");
    }

    public function register(Request $request){
        $request->validate([
            'login' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        $data = $request->all();
        $check = $this->createUser($data);

        return redirect("dashboard")->with("status", "Utworzono nowy login");
    }
    public function createUser(array $data){
        return User::create([
            'login' => $data['login'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}
