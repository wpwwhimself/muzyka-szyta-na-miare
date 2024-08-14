<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FileTag extends Component
{
    public $tag;
    public $props;

    /**
     * list of available tags
     */
    public const OPTIONS = [
        "c" => ["click", "wersja z metronomem", "⏰"],
        "d" => ["demo", "wersja demonstracyjna", "🚧"],
        "m" => ["melody", "wersja z linią melodyczną", "🎵"],
        "v" => ["vocal", "wersja z linią wokalną", "🎙️"],
        "t" => ["transpose", "transpozycja względem oryginału", null],
    ];
    public const REGEX = "/([cdmv]|t[+-]?\d+)/";

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($tag)
    {
        $options = self::OPTIONS;

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
