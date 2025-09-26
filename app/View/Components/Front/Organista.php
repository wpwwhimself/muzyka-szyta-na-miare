<?php

namespace App\View\Components\Front;

use App\Models\OrganShowcase;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Organista extends Component
{
    public $showcases;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->showcases = OrganShowcase::orderBy("created_at", "desc")->limit(5)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front.organista');
    }
}
