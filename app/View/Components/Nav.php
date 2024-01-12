<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Nav extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $home;
    public $logged;
    public $archmage;
    public function __construct()
    {
        $this->home = [
            ["link" => "#offer", "label" => "Oferta"],
            ["link" => "#recomms", "label" => "Opinie"],
            ["link" => "#showcases", "label" => "Realizacje"],
            ["link" => "#prices", "label" => "Cennik"],
            ["link" => "#about", "label" => "O mnie"],
            ["link" => "#contact", "label" => "Złóż zamówienie"],
        ];
        $this->logged = [
            ["link" => route("dashboard"), "label" => "Pulpit", "icon" => "fa-solid fa-house-chimney-user"],
            ["link" => route("quests"), "label" => "Zlecenia", "icon" => "fa-solid fa-boxes-stacked"],
            ["link" => route("requests"), "label" => "Zapytania", "icon" => "fa-solid fa-envelope-open-text"],
            ["link" => route("prices"), "label" => "Cennik", "icon" => "fa-solid fa-barcode"],
        ];
        $this->archmage = [
            ["link" => route("songs"), "label" => "Utwory", "icon" => "fa-solid fa-compact-disc"],
            ["link" => route("clients"), "label" => "Klienci", "icon" => "fa-solid fa-users"],
            ["link" => route("finance"), "label" => "Finanse", "icon" => "fa-solid fa-sack-dollar"],
            ["link" => route("showcases"), "label" => "Reklama", "icon" => "fa-solid fa-bullhorn"],
            ["link" => route("stats"), "label" => "Statystyki", "icon" => "fa-solid fa-chart-line"],
            ["link" => route("ppp"), "label" => "PPP", "icon" => "fa-solid fa-circle-question"],
            ["link" => route("settings"), "label" => "Ustawienia", "icon" => "fa-solid fa-cog"],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.nav');
    }
}
