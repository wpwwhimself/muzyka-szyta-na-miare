<?php

namespace App\View\Components;

use App\Models\Quest;
use App\Models\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Calendar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $calendar;
    public function __construct()
    {
        $available_days_needed = setting("available_days_needed");
        $available_days_count = 0;
        $suggestion_ready = 0;
        for($i = 1; $i < setting("calendar_length"); $i++){
            $date = strtotime("+$i day");
            $workday_type = workday_type(date("w", $date));
            $quests = Quest::where("deadline", date("Y-m-d", $date))->get();
            $requests = Request::where("deadline", date("Y-m-d", $date))->whereNotIn("status_id", [7,8,9])->get();

            $items_count = count($quests) + count($requests);
            if($items_count < setting("available_day_until") && $workday_type == "") $available_days_count++;
            if($available_days_count == $available_days_needed && $suggestion_ready === 0) $suggestion_ready = 1;

            $this->calendar[date("d.m", $date)] = [
                "day_type" => $workday_type,
                "date_val" => date("Y-m-d", $date),
                "quests" => $quests,
                "requests" => $requests,
                "suggest_date" => ($suggestion_ready == 1),
            ];

            // if a suggestion is made, no more is needed
            if($suggestion_ready === 1) $suggestion_ready = 2;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.calendar');
    }
}
