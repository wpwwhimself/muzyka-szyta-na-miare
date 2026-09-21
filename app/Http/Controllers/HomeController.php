<?php

namespace App\Http\Controllers;

use App\Models\DjShowcase;
use App\Models\Genre;
use App\Models\OrganShowcase;
use Illuminate\Support\Facades\Blade;

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

    #region front services
    public function loadFrontService(string $service_name)
    {
        $html = Blade::render("<x-front.$service_name />");

        return response()->json(compact("html"));
    }
    #endregion
}
