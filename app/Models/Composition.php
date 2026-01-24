<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\View\ComponentAttributeBag;
use Mattiverse\Userstamps\Traits\Userstamps;

class Composition extends Model
{
    //

    public const META = [
        "label" => "Kompozycje",
        "icon" => "music",
        "description" => "Byty nadrzędne dla utworów. Każda kompozycja może mieć wiele wykonań, przechowywanych jako utwory.",
        "role" => "archmage",
        "ordering" => 1,
        "defaultSort" => "title",
    ];

    protected $fillable = [
        "title",
        "composer",
        "lyrics",
        "melody",
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
            get: fn () => view("components.shipyard.ui.button", [
                "icon" => "bullhorn",
                "pop" => "Podglad w katalogu",
                "action" => route("catalog", ["composition" => $this->id]),
                "attributes" => new ComponentAttributeBag([
                    "target" => "_blank",
                ]),
                "slot" => null,
            ])
            . view("components.shipyard.app.model.connections-preview", [
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
            "label" => "Tytuł",
            "icon" => "badge-account",
        ],
        "composer" => [
            "type" => "text",
            "label" => "Kompozytor",
            "icon" => "account-music",
        ],
        "lyrics" => [
            "type" => "TEXT",
            "label" => "Tekst",
            "icon" => "playlist-music",
            "hint" => "Słowa utworu – wypełnienie umożliwia wyszukiwanie tej kompozycji w panelu DJa.",
        ],
        "melody" => [
            "type" => "ABC",
            "label" => "Linia melodyczna",
            "icon" => "music-clef-treble",
        ],
    ];

    public const CONNECTIONS = [
        "songs" => [
            "model" => Song::class,
            "mode" => "many",
            "readonly" => true,
            // "field_name" => "",
            "field_label" => "Przypisane:",
        ],
        "tags" => [
            "model" => SongTag::class,
            "mode" => "many",
        ],
    ];

    public const ACTIONS = [
        [
            "icon" => "bullhorn",
            "label" => "Podgląd w katalogu",
            "show-on" => "edit",
            "route" => "catalog",
            "params" => ["composition" => "id"],
            "role" => "technical",
        ],
    ];
    #endregion

    // use CanBeSorted;
    public const SORTS = [
        "title" => [
            "label" => "Tytuł",
            "compare-using" => "field",
            "discr" => "title",
        ],
        "composer" => [
            "label" => "Kompozytor",
            "compare-using" => "field",
            "discr" => "composer",
        ],
    ];

    public const FILTERS = [
        "title" => [
            "label" => "Tytuł",
            // "icon" => "badge-account",
            "compare-using" => "field",
            "discr" => "title",
            "type" => "text",
            "operator" => "regexp",
        ],
        "composer" => [
            "label" => "Kompozytor",
            // "icon" => "badge-account",
            "compare-using" => "field",
            "discr" => "composer",
            "type" => "text",
            "operator" => "regexp",
        ],
        "djready" => [
            "label" => "Gotowy dla DJ",
            "icon" => "headphones",
            "compare-using" => "function",
            "discr" => "is_dj_ready",
            "type" => "select",
            // "operator" => "=",
            "selectData" => [
                "options" => [
                    ["label" => "Tak", "value" => 1],
                    ["label" => "Nie", "value" => 0],
                ],
                "emptyOption" => "Wszystkie",
            ],
        ],
    ];

    #region scopes
    use HasStandardScopes;

    public function scopeForConnection($query)
    {
        return $this->orderBy("title")
            ->orderBy("composer");
    }
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

    public function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(' – ', array_filter([
                $this->composer,
                $this->title ?? 'utwór bez tytułu',
            ], fn($v) => !empty($v))),
        );
    }

    public function isDjReady(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->lyrics),
        );
    }
    #endregion

    #region relations
    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function tags()
    {
        return $this->belongsToMany(SongTag::class);
    }
    #endregion

    #region helpers
    #endregion
}
