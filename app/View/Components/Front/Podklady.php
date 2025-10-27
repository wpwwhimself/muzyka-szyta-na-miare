<?php

namespace App\View\Components\Front;

use App\Models\ClientShowcase;
use App\Models\Genre;
use App\Models\Price;
use App\Models\QuestType;
use App\Models\Showcase;
use App\Models\Song;
use App\Models\SongTag;
use App\Models\StatusChange;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Podklady extends Component
{
    public $showcases;
    public $client_showcases;
    public $pinned_comments;
    public $prices;
    public $quest_types;
    public $average_quest_done;
    public $random_song;
    public $contact_preferences;
    public $genres;
    public $song_tags;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->showcases = Showcase::orderBy("created_at", "desc")->limit(5)->get();
        $this->client_showcases = ClientShowcase::orderBy("updated_at", "desc")->limit(3)->get();
        $this->pinned_comments = StatusChange::where("pinned", true)->orderBy("date", "desc")->get();

        $this->prices = Price::where("operation", "+")->get(["service", "quest_type_id", "price_".strtolower(CURRENT_PRICING())." AS price"]);

        $quest_types_raw = QuestType::all()->toArray();
        foreach($quest_types_raw as $val){
            $this->quest_types[$val["id"]] = $val["type"];
        }

        $diffs = [];
        foreach(StatusChange::whereIn("new_status_id", [11, 15])->orderBy("date")->get() as $stts){
            $diffs[$stts->re_quest_id] =
                (isset($diffs[$stts->re_quest_id])) ?
                (
                    (is_numeric($diffs[$stts->re_quest_id])) ?
                    $diffs[$stts->re_quest_id] : //jeśli już jest policzone, to zostaw
                    $diffs[$stts->re_quest_id]->diffInDays(Carbon::parse($stts->date)) + 1
                ) :
                Carbon::parse($stts->date);
        }
        $diffs = array_filter($diffs, function($val){ return is_numeric($val); });
        $this->average_quest_done = (count($diffs) == 0) ? 0 : round(array_sum($diffs)/count($diffs));

        $this->random_song = Song::all()->random();

        $this->contact_preferences = [
            "email" => "email",
            "telefon" => "telefon",
            "sms" => "SMS",
            "inne" => "inne"
        ];

        $this->genres = Genre::orderBy("name")->get();
        $this->song_tags = SongTag::orderBy("name")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.front.podklady');
    }
}
