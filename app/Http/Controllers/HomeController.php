<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        // return redirect("/auth");
        return view("front", [
            "title" => null,
            "forWhom" => "guest",
            "extraCss" => "front"
        ]);
    }
}
