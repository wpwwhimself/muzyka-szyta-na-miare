<?php

namespace App\View\Components\Front;

use App\Models\DjShowcase;
use App\Models\Genre;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dj extends Component
{
    public $showcases;
    public $genres;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->showcases = DjShowcase::orderBy("created_at", "desc")->limit(5)->get();
        $this->genres = Genre::orderBy("name")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front.dj');
    }
}
