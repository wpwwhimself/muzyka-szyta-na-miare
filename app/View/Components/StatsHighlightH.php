<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatsHighlightH extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $data1; public $data2;
    public $title;
    public $bracketedNumbers;
    public function __construct($data, $title = null, $bracketedNumbers = null)
    {
        $this->data1 = $data;
        $this->title = $title;
        $this->bracketedNumbers = $bracketedNumbers;

        switch($bracketedNumbers){
            case "percentages":
                $this->data1 = $data->split;
                $this->data2 = $data->total;
                break;
            case "comparison":
                $this->data1 = $data->main;
                $this->data2 = $data->difference;
                break;
            default:
                $this->data1 = $data;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.stats-highlight-h');
    }
}
