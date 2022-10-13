<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $type;
    public $name;
    public $label;
    public $autofocus;
    public $required;
    public $selected;

    public function __construct($type, $name, $label, $autofocus = false, $required = false, $selected = false)
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->autofocus = $autofocus;
        $this->required = $required;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}
