<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class ExtendoBlock extends Component
{
    public $key;
    public $noEdit;
    public $header;
    public $headerIcon;
    public $warning;
    public $extended;

    public $icon;
    public $iconLabel;
    public $textBelowIcon;

    public $title;
    public $subtitle;

    public $listData;
    public $sectionData;

    /**
     * Dictionary of header types
     * type => [header, icon]
     */
    static $TYPES = [
        "song" => ["Utwór", "compact-disc"],
        "client" => ["Klient", "user"],
        "request" => ["Zapytanie", "envelope"],
        "quest" => ["Zlecenie", "box"],
    ];

    /**
     * Create a new component instance.
     *
     * @param key id for the component
     * @param type type of component -- song, client, request or quest
     * @param object object to draw data from -- if missing, then...?
     * @param extended should the drawer be already extended? false / true / 'perma'
     *
     * @return void
     */
    public function __construct($key, $type, $object = null, $warning = null, $noEdit = false, $extended = false)
    {
        $this->key = $key;
        [$this->header, $this->headerIcon] = self::$TYPES[$type];
        $this->warning = $warning;
        $this->noEdit = $noEdit;
        $this->extended = $extended;

        if($object){
            switch($type){
                case "song":
                    $this->icon = preg_replace("/fa-/", "", $object->type->fa_symbol);
                    $this->iconLabel = $object->type->type;
                    $this->textBelowIcon = $object->id;
                    $this->title = $object->title ?? 'utwór bez tytułu';
                    $this->subtitle = $object->artist;
                    $this->listData = array_filter([
                        "linki" => $this->objectsToLinks(explode(",", $object->link), "songlink"),
                        "kod wyceny" => is_archmage() ? $object->price_code : null,
                        "w zleceniach" => is_archmage() ? $this->objectsToLinks($object->quests, "quest") : null,
                    ], fn($v) => !empty($v));
                    $this->sectionData = array_filter([
                        "notatki" => $object->notes,
                        "czas wyk." => is_archmage() ? $object->workTime : null,
                    ], fn($v) => !empty($v));
                    break;
            }
        }
    }

    private function objectsToLinks($collection, $className){
        $output = [];
        $link = "";
        $label = "";

        foreach($collection as $key => $obj){
            switch($className){
                case "quest": $link = $obj->linkTo; $label = $obj->id; break;
                case "songlink": $link = $obj; $label = "[".($key + 1)."]"; break;
            }
            $output[] = "<a target='_blank' href='$link'>$label</a>";
        }

        return implode(", ", $output);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.extendo-block');
    }
}
