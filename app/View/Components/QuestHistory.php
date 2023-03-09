<?php

namespace App\View\Components;

use App\Models\Client;
use App\Models\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use App\Models\Status;

class QuestHistory extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $quest;
    public $history;
    public function __construct($quest)
    {
        $this->quest = $quest;
        $this->history = DB::table("status_changes")
            ->whereIn("re_quest_id", [$quest->id, Request::where("quest_id", $quest->id)->value("id")])
            ->orderBy("date", "desc")
            ->orderBy("new_status_id", "desc")
            ->get();
    }

    public function statusName($id){
        return Status::find($id)->status_name;
    }
    public function statusSymbol($id){
        $symbol = Status::find($id)->status_symbol;
        if($id >= 100) return $symbol;
        return "<i class='fa-solid $symbol'></i>";
    }
    public function clientName($id){
        if($id == 1) return "Wojciech Przybyła";
        if($id == null) return $this->quest->client_name;
        return Client::find($id)->client_name;
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
