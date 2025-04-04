<?php

namespace App\View\Components;

use App\Models\Quest;
use App\Models\Request;
use App\Models\Song;
use App\Models\User;
use Illuminate\View\Component;

class QuestMini extends Component
{
    public bool $is_request;
    public Song|Request $song;
    public User|string|null $client;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public Quest|Request $quest,
        public ?int $no = null,
    )
    {
        $this->quest = $quest;
        $this->is_request = $quest instanceof Request;
        $this->song = $this->is_request ? $this->quest : $this->quest->song;
        $this->client = $this->quest->client ?? $this->quest->client_name;
        $this->no = $no;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.quest-mini');
    }
}
