<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FileTag extends Component
{
    public $tag;
    public $props;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($tag)
    {
        $options = [
            "c" => ["click", "wersja z metronomem", "⏰"],
            "d" => ["demo", "wersja demonstracyjna", "🚧"],
            "m" => ["melody", "wersja z linią melodyczną", "🎵"],
            "t" => ["transpose", "transpozycja względem oryginału", null],
        ];

        $this->tag = $tag;
        $this->props = $options[$tag[0]];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.file-tag');
    }
}
