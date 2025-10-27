<?php

namespace App\View\Components;

use App\Models\Request;
use Illuminate\View\Component;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Mail\Markdown;

class QuestHistory extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $quest;
    public $history;
    public $extended;

    public function __construct($quest, $extended = false)
    {
        $this->quest = $quest;
        $this->history = StatusChange::whereIn("re_quest_id", [$quest->id, Request::where("quest_id", $quest->id)->value("id")])
            ->orderBy("date")
            ->orderBy("new_status_id")
            ->get()
        ;
        $this->extended = $extended;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.quest-history');
    }
}
