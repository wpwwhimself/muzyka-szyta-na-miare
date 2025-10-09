<?php

namespace App\View\Components;

use App\Models\Status;
use Illuminate\View\Component;

class PhaseIndicator extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $statusId;
    public $small;

    public function __construct($statusId, $small = false)
    {
        $this->statusId = $statusId;
        $this->small = $small;
    }

    public function statusName($statusId){
        return Status::find($statusId)->status_name;
    }
    public function statusSymbol($statusId){
        return Status::find($statusId)->icon;
    }

    public function bars($statusId){
        /**
         * - nowy
         * - kompletowanie informacji
         * - wycena
         * - finał
         */
        $bar_count = ($statusId % 10);
        return $bar_count;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.phase-indicator');
    }
}
