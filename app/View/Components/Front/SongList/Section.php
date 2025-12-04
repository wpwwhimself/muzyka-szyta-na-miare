<?php

namespace App\View\Components\Front\SongList;

use App\Models\Genre;
use App\Models\SongTag;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Section extends Component
{
    public $genres;
    public $song_tags;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $for,
    ) {
        $this->for = $for;

        $this->genres = Genre::orderBy("name")->get();
        $this->song_tags = SongTag::orderBy("name")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front.song-list.section');
    }
}
