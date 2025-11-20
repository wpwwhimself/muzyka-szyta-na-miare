<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;

class Song extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Utwory",
        "icon" => "disc",
        "description" => "Przetworzone utwory muzyczne.",
        "role" => "technical",
        "ordering" => 1,
    ];

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "title", "artist",
        "genre_id",
        "link",
        "price_code", "notes",
        "has_recorded_reel", "has_original_mv",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->full_title;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_title,
        );
    }

    public function displayTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.h", [
                "lvl" => 3,
                "icon" => $this->icon ?? self::META["icon"],
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                ]),
                "slot" => $this->full_title,
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.model.badges", [
                "badges" => $this->badges,
            ])->render(),
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.model.connections-preview", [
                "connections" => self::getConnections(),
                "model" => $this,
            ])->render(),
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "title" => [
            "type" => "text",
            "label" => "Tytuł utworu",
            "icon" => "music-box",
            "required" => true,
        ],
        "artist" => [
            "type" => "text",
            "label" => "Wykonawca",
            "icon" => "account-music"
        ],
        "link" => [
            "type" => "text",
            "label" => "Linki do utworu",
            "icon" => "link",
            "hint" => "Oddzielone przecinkami",
        ],
        "price_code" => [
            "type" => "text",
            "label" => "Kod wyceny",
            "icon" => "barcode",
        ],
        "notes" => [
            "type" => "TEXT",
            "label" => "Życzenia dot. utworu",
            "icon" => "cloud",
            "hint" => "np. styl itp.",
        ],
        "has_recorded_reel" => [
            "type" => "checkbox",
            "label" => "Nagrałem się do rolki",
            "icon" => "account-voice",
        ],
        "has_original_mv" => [
            "type" => "checkbox",
            "label" => "Oryginał ma teledysk",
            "icon" => "movie-open",
        ],
    ];

    public const CONNECTIONS = [
        "type" => [
            "model" => QuestType::class,
            "mode" => "one",
            "field_name" => "quest_type_id",
            "field_label" => "Typ zlecenia",
        ],
        "genre" => [
            "model" => Genre::class,
            "mode" => "one",
            "field_label" => "Gatunek",
        ],
        "quests" => [
            "model" => Quest::class,
            "mode" => "many",
            "readonly" => true,
        ],
    ];

    public const ACTIONS = [
        [
            "icon" => "wrench",
            "label" => "Edytuj po staremu",
            "show-on" => "edit",
            "route" => "song-edit",
        ],
    ];
    #endregion

    // use CanBeSorted;
    public const SORTS = [
        "title" => [
            "label" => "tytuł",
            "compare-using" => "field",
            "discr" => "title",
        ],
        // "<name>" => [
        //     "label" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        // ],
    ];

    public const FILTERS = [
        "id" => [
            "label" => "ID",
            // "icon" => "",
            "compare-using" => "field",
            "discr" => "id",
            "type" => "text",
            "operator" => "regexp",
        ],
        "title" => [
            "label" => "Tytuł",
            "compare-using" => "field",
            "discr" => "title",
            "type" => "text",
            "operator" => "regexp",
        ],
        "artist" => [
            "label" => "Wykonawca",
            "compare-using" => "field",
            "discr" => "artist",
            "type" => "text",
            "operator" => "regexp",
        ],
    ];

    #region scopes
    use HasStandardScopes;
    #endregion

    #region attributes
    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected $appends = [
        "full_title",
        "has_showcase_file",
        "work_time_total",
        "now_working",
    ];

    use HasStandardAttributes;

    // public function badges(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => [
    //             [
    //                 "label" => "",
    //                 "icon" => "",
    //                 "class" => "",
    //                 "style" => "",
    //                 "condition" => "",
    //             ],
    //             [
    //                 "html" => "",
    //             ],
    //         ],
    //     );
    // }

    public function getCostsAttribute() {
        return MoneyTransaction::where("description", "like", "%".$this->id."%")
            ->orderByDesc("created_at")
            ->get();
    }
    public function getWorkTimeTotalAttribute(){
        return CarbonInterval::seconds($this->hasMany(SongWorkTime::class)->sum(DB::raw("TIME_TO_SEC(time_spent)")))
            ->cascade()
            ->format("%h:%I:%S")
        ;
    }
    public function getNowWorkingAttribute() {
        return $this->workTime
            ->filter(fn($log) => $log->now_working)
            ->count()
            > 0
        ;
    }
    public function getHasShowcaseFileAttribute(){
        return Storage::exists("showcases/$this->id.ogg");
    }
    public function getHasSafeFilesAttribute() {
        return Storage::exists("safe/$this->id");
    }
    public function getLinkToAttribute(){
        return route("songs", ["search" => $this->id]);
    }
    public function getFullTitleAttribute(){
        return implode(' – ', array_filter([
            $this->artist,
            $this->title ?? 'utwór bez tytułu',
        ], fn($v) => !empty($v)));
    }
    public function getTypeLetterAttribute(){
        return substr($this->id, 0, 1);
    }
    #endregion

    #region relations
    public function genre(){
        return $this->belongsTo(Genre::class);
    }
    public function showcase(){
        return $this->hasOne(Showcase::class);
    }
    public function clientShowcase() {
        return $this->hasMany(ClientShowcase::class);
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    public function workTime() {
        return $this->hasMany(SongWorkTime::class)->orderByDesc("time_spent");
    }
    public function tags() {
        return $this->belongsToMany(SongTag::class);
    }
    public function files()
    {
        return $this->hasMany(File::class)->orderByDesc("updated_at");
    }
    public function type()
    {
        return $this->belongsTo(QuestType::class, "type_letter", "code");
    }
    #endregion
}
