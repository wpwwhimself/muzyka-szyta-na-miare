<?php

namespace App\View\Components;

use App\Models\Quest;
use App\Models\Request;
use Illuminate\View\Component;

class Calendar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $calendar;
    public $withToday;
    public $clickDays;
    public $length;
    public function __construct($withToday = false, $clickDays = true, $length = 14)
    {
        $this->clickDays = $clickDays;

        $available_days_needed = setting("available_days_needed");
        $available_days_count = 0;
        $suggestion_ready = 0;
        for($i = ($withToday ? 0 : 1); $i < $length; $i++){
            $date = strtotime("+$i day");
            $workday_type = workday_type(date("w", $date));
            $quests = Quest::where("deadline", date("Y-m-d", $date))->whereIn("status_id", [11, 12, 16, 26])->get();
            $quests_done = Quest::where("deadline", date("Y-m-d", $date))->whereIn("status_id", [15])->get();
            $requests = Request::where("deadline", date("Y-m-d", $date))->whereNotIn("status_id", [7,8,9])->get();

            $items_count = count($quests) + count($requests);
            if($items_count < setting("available_day_until") && $workday_type == "") $available_days_count++;
            if($available_days_count == $available_days_needed && $suggestion_ready === 0) $suggestion_ready = 1;

            $this->calendar[date("d.m", $date)] = [
                "day_type" => $workday_type,
                "date_val" => date("Y-m-d", $date),
                "quests" => $quests,
                "quests_done" => $quests_done,
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
