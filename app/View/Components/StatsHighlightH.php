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
    public $allPln;
    public function __construct($data, $title = null, $bracketedNumbers = null, $allPln = false)
    {
        $this->data1 = $data;
        $this->title = $title;
        $this->bracketedNumbers = $bracketedNumbers;
        $this->allPln = $allPln;

        switch($bracketedNumbers){
            case "percentages":
                $this->data1 = $data->split;
                $this->data2 = $data->total;
                break;
            case "comparison":
                $this->data1 = $data->main;
                //calculate differences
                $this->data2 = json_decode(json_encode(array_combine(
                    array_keys(get_object_vars($data->main)),
                    array_map(
                        fn($main, $comp) => $main - $comp,
                        get_object_vars($data->main),
                        get_object_vars($data->compared_to),
                    )
                )));
                break;
            case "comparison-raw":
                $this->data1 = $data->main;
                //calculate differences
                $this->data2 = json_decode(json_encode(array_combine(
                    array_keys(get_object_vars($data->main_raw)),
                    array_map(
                        fn($main, $comp) => $main - $comp,
                        get_object_vars($data->main_raw),
                        get_object_vars($data->compared_to_raw),
                    )
                )));
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
