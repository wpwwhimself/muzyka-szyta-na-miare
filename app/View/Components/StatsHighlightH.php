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

        // if(preg_match("/satur/i", $title)) dd($data);
        switch($bracketedNumbers){
            case "percentages":
                $this->data1 = $data["split"];
                $this->data2 = $data["total"];
                break;
            case "comparison":
                $this->data1 = $data["main"];
                //calculate differences
                $this->data2 = json_decode(json_encode(array_combine(
                    is_array($data["main"]) ? array_keys($data["main"]) : $data["main"]->keys()->toArray(),
                    array_map(
                        fn($main, $comp) => $main - $comp,
                        is_array($data["main"]) ? $data["main"] : $data["main"]->toArray(),
                        is_array($data["compared_to"]) ? $data["compared_to"] : $data["compared_to"]->toArray(),
                    )
                )));
                break;
            case "comparison-raw":
                $this->data1 = $data["main"];
                //calculate differences
                $this->data2 = json_decode(json_encode(array_combine(
                    $data["main_raw"]->keys()->toArray(),
                    array_map(
                        fn($main, $comp) => $main - $comp,
                        is_array($data["main_raw"]) ? $data["main_raw"] : $data["main_raw"]->toArray(),
                        is_array($data["compared_to_raw"]) ? $data["compared_to_raw"] : $data["compared_to_raw"]->toArray(),
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
