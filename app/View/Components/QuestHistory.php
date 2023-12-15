<?php

namespace App\View\Components;

use App\Models\Client;
use App\Models\Request;
use Illuminate\View\Component;
use App\Models\Status;
use App\Models\StatusChange;
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

    public function statusName($id){
        return Status::find($id)->status_name;
    }
    public function clientName($id){
        if($id == 1) return "Wojciech Przybyła";
        if($id == null) return _ct_($this->quest->client_name);
        return _ct_(Client::find($id)->client_name);
    }
    public function entryLabel(StatusChange $entry){
        $output = "";
        $details = [];
        if($entry->values) foreach(json_decode($entry->values) as $key => $val){
            $details[] = "- **$key**: $val";
        }
        $content = [
            "<span class='quest-status p-".$entry->status->id."'><i class='fas ".$entry->status->status_symbol."'></i> ".$entry->status->status_name."</span>",
            count($details) ? implode("\n", $details) : null,
            $entry->new_status_id == 32 ? as_pln($entry->comment) : $entry->comment,
            "<span class='grayed-out'>".$this->clientName($entry->changed_by).", ".$entry->date."</span>",
        ];
        foreach($content as $value){
            if(empty($value)) continue;
            $value = preg_replace('/"/', "&quot;", $value);
            $output .= Markdown::parse($value);
        }
        return $output;
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
