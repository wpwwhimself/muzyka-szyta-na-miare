<?php

namespace App\View\Components;

use App\Models\CalendarFreeDay;
use App\Models\Quest;
use App\Models\Request;
use Carbon\Carbon;
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
    public $suggest;
    private $available_day_until;
    public function __construct($withToday = false, $clickDays = true, $suggest = true)
    {
        $this->clickDays = $clickDays;
        $this->suggest = $suggest;
        $this->available_day_until = explode(",", setting("available_day_until"));

        $available_days_needed = setting("available_days_needed");
        $length = max(
            7,
            7 - Quest::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays(Carbon::now(), false),
            7 - Request::orderByDesc("deadline")
                ->first()
                ->deadline
                ->diffInDays(Carbon::now(), false),
        );

        $available_days_count = 0;
        $suggestion_ready = 0;
        for($i = ($withToday ? 0 : 1); $i < $length; $i++){
            $date = strtotime("+$i day");
            $workday_type = $this->workday_type($date);
            $quests = Quest::where("deadline", date("Y-m-d", $date))->whereNotIn("status_id", [15, 17, 18, 19])->get();
            $quests_done = Quest::where("deadline", date("Y-m-d", $date))->whereIn("status_id", [15])->get();
            $requests = Request::where("deadline", date("Y-m-d", $date))->whereNotIn("status_id", [7,8,9])->get();

            $items_count = count($quests) + count($requests);
            if(
                $items_count < $this->available_day_until[date("w", $date)] &&
                (!preg_match("/free/", $workday_type))
            ) $available_days_count++;
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
     * Klasy dni pracujących
     */
    private function workday_type($day){
        $day_no = date("w", $day);
        $workdays_free = array_keys(array_filter($this->available_day_until, fn($el) => $el == 0));
        $weekend = [0, 6];
        $is_free_day = !!(CalendarFreeDay::whereDate("date", Carbon::parse($day))->first());

        $return = [];
        if(in_array($day_no, $workdays_free) || $is_free_day || (in_array($day_no, $weekend) && !setting("work_on_weekends"))) $return[] = "free";
        if(in_array($day_no, $weekend)) $return[] = "weekend";
        return implode(" ", $return);
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
