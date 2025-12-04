<?php

namespace App\Http\Controllers;

use App\Models\DjShowcase;
use App\Models\Genre;
use App\Models\OrganShowcase;

class HomeController extends Controller
{
    public function index()
    {
        return view("front.index");
    }

    public function catalog()
    {
        return view("front.catalog");
    }
}
