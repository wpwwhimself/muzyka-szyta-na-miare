<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class ExtendoBlock extends Component
{
    public $key;
    public $noEdit;
    public $headerIcon;
    public $title;
    public $subtitle;
    public $warning;
    public $extended;

    /**
     * Create a new component instance.
     *
     * @param key id for the component
     * @param type type of component -- song, client, request or quest
     * @param object object to draw data from -- if missing, then...?
     * @param extended should the drawer be already extended? false / true / 'perma'
     *
     * @return void
     */
    public function __construct(
        $key,
        $title = null,
        $subtitle = null,
        $headerIcon = null,
        $warning = null,
        $extended = false
    ) {
        $this->key = $key;
        $this->headerIcon = $headerIcon;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->warning = $warning;
        $this->extended = $extended;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.extendo-block');
    }
}
