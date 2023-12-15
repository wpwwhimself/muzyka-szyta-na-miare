<?php

namespace App\View\Components;

use App\Models\SongWorkTime;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class SongWorkTimeLog extends Component
{
    public $quest;
    public $workhistory;
    public $stats_statuses;
    public $extended;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($quest, $extended = false)
    {
        $this->quest = $quest;
        $this->workhistory = SongWorkTime::where("song_id", $quest->song_id)->orderBy("status_id")->get();
        $this->stats_statuses = DB::table("statuses")->where("id", ">=", 100)->orderByDesc("status_name")->get()->toArray();
        $this->extended = $extended;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.song-work-time-log');
    }
}
