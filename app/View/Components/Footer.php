<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Footer extends Component
{
    public array $socials;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->socials = [
            ["facebook", "https://www.facebook.com/muzykaszytanamiarepl"],
            ["youtube", "https://www.youtube.com/@muzykaszytanamiarepl"],
            ["tiktok", "https://tiktok.com/@muzykaszytanamiarepl"],
            ["instagram", "https://www.instagram.com/muzykaszytanamiarepl/"],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.footer');
    }
}
