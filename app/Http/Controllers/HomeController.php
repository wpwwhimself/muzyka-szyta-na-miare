<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view("front", [
            "title" => null,
            "forWhom" => "guest",
            "extraCss" => "front"
        ]);
    }

    public function dashboard(){
        return view("front", [
            "title" => null,
            "forWhom" => "archmage",
            "extraCss" => "front"
        ]);
    }
}
