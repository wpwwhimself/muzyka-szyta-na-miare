<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class SongWorkTime extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Czasy pracy nad utworami",
        "icon" => "wrench-clock",
        "description" => "",
        "role" => "archmage",
        "ordering" => 9,
        "defaultSort" => "-date",
    ];

    protected $fillable = [
        "song_id",
        "status_id",
        "time_spent",
        "now_working",
        "since",
    ];

    #region presentation
    public function __toString(): string
    {
        return implode(" | ", [
            $this->song->full_title,
            $this->status->status_name,
        ]);
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->__toString(),
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
                "slot" => $this->__toString(),
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
        // "<column_name>" => [
        //     "type" => "<input_type>",
        //     "column-types" => [ // for JSON
        //         "<label>" => "<input_type>",
        //     ],
        //     "label" => "",
        //     "hint" => "",
        //     "icon" => "",
        //     // "required" => true,
        //     // "autofill-from" => ["<route>", "<model_name>"],
        //     // "character-limit" => 999, // for text fields
        //     // "hide-for-entmgr" => true,
        //     // "role" => "",
        // ],
    ];

    public const CONNECTIONS = [
        // "<name>" => [
        //     "model" => ,
        //     "mode" => "<one|many>",
        //     // "field_name" => "",
        //     // "field_label" => "",
        // ],
    ];

    public const ACTIONS = [
        // [
        //     "icon" => "",
        //     "label" => "",
        //     "show-on" => "<list|edit>",
        //     "route" => "",
        //     "role" => "",
        //     "dangerous" => true,
        // ],
    ];
    #endregion

    // use CanBeSorted;
    public const SORTS = [
        "date" => [
            "label" => "Data rozpoczęcia",
            "compare-using" => "field",
            "discr" => "since",
        ],
        "status" => [
            "label" => "Kategoria",
            "compare-using" => "field",
            "discr" => "status_id",
        ],
    ];

    public const FILTERS = [
        "song" => [
            "label" => "Utwór",
            "icon" => "compact-disc",
            "compare-using" => "field",
            "discr" => "song_id",
            "mode" => "one",
            "type" => "select",
            "selectData" => [
                "optionsFromScope" => [
                    Song::class,
                    "forConnection",
                ],
            ],
        ],
    ];

    #region scopes
    use HasStandardScopes;
    #endregion

    #region attributes
    protected $casts = [
        "since" => "datetime",
    ];

    protected $appends = [

    ];

    public $timestamps = false;

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
    #endregion

    #region relations
    public function song() {
        return $this->belongsTo(Song::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }
    #endregion

    #region helpers
    #endregion
}
