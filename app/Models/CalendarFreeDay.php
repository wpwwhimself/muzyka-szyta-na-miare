<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class CalendarFreeDay extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Dni wolne w grafiku",
        "icon" => "calendar-lock",
        "description" => "Oznaczenia dni jako wolnych. Te dni nie będą brane pod uwagę podczas sugerowania terminów realizacji questów.",
        "role" => "technical",
        "ordering" => 81,
    ];

    protected $fillable = [
        "date"
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->date->format("Y-m-d");
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date->format("Y-m-d"),
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
                "slot" => $this->date->format("Y-m-d"),
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            get: fn () => null,
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => null,
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "date" => [
            "type" => "date",
            "label" => "Data",
            "icon" => "calendar",
            "required" => true,
        ],
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

    #region scopes
    use HasStandardScopes;
    #endregion

    protected $casts = [
        "date" => "datetime",
    ];
    const UPDATED_AT = null;
}
