<?php

namespace App\View\Components;

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
    public $scissors;

    /**
     * Create a new component instance.
     *
     * @param key id for the component
     * @param type type of component -- song, client, request or quest
     * @param object object to draw data from -- if missing, then...?
     * @param extended should the drawer be already extended? false / true / 'perma'
     * @param warning should the block display a warning? array of warning messages => conditions
     *
     * @return void
     */
    public function __construct(
        $key,
        $title = null,
        $subtitle = null,
        $headerIcon = null,
        $warning = null,
        $extended = false,
        $scissors = false,
    ) {
        $this->key = $key;
        $this->headerIcon = $headerIcon;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->warning = $warning;
        $this->extended = $extended;
        $this->scissors = $scissors;
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
