<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\View\ComponentAttributeBag;
use Mattiverse\Userstamps\Traits\Userstamps;

class MoneyTransaction extends Model
{
    //

    public const META = [
        "label" => "Transakcje",
        "icon" => "cash-register",
        "description" => "Wpłaty i wydatki.",
        "role" => "",
        "ordering" => 81,
    ];

    use SoftDeletes, Userstamps, HasUuids;

    protected $fillable = [
        "typable_type", "typable_id",
        "relatable_type", "relatable_id",
        "date",
        "amount",
        "description",
        "is_hidden",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->name;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
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
                "slot" => $this->name,
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
        "date" => [
            "type" => "date",
            "label" => "Data transakcji",
            "hint" => "",
            "icon" => "calendar",
            "required" => true,
        ],
        "amount" => [
            "type" => "number",
            "label" => "Kwota",
            "hint" => "",
            "icon" => "cash",
            "required" => true,
            "step" => 0.01,
        ],
        "description" => [
            "type" => "TEXT",
            "label" => "Opis",
            "hint" => "",
            "icon" => "text",
        ],
        "is_hidden" => [
            "type" => "checkbox",
            "label" => "Ukryta",
            "hint" => "Ukryte transakcje nie są liczone do podsumowań, statystyk i symulacji.",
            "icon" => "eye-off",
        ],
    ];

    public const CONNECTIONS = [
        "typable" => [
            "model" => [
                CostType::class,
                IncomeType::class,
            ],
            "mode" => "one",
            // "field_name" => "",
            // "field_label" => "",
        ],
        "relatable" => [
            "model" => [
                Quest::class,
            ],
            "mode" => "one",
            // "field_name" => "",
            // "field_label" => "",
        ],
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
        // "<name>" => [
        //     "label" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        // ],
    ];

    public const FILTERS = [
        // "<name>" => [
        //     "label" => "",
        //     "icon" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        //     "type" => "<input type>",
        //     "options" => [
        //         "<label>" => <value>,
        //     ],
        // ],
    ];

    #region scopes
    use HasStandardScopes;
    #endregion

    #region attributes
    protected function casts(): array
    {
        return [
            "date" => "date",
        ];
    }

    protected $appends = [

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
    #endregion

    #region relations
    public function typable()
    {
        return $this->morphTo();
    }

    public function relatable()
    {
        return $this->morphTo();
    }

    public function invoice()
    {
        return $this->hasManyThrough(
            Invoice::class,
            InvoiceQuest::class,
            "quest_id", "id",
            "relatable_id", "invoice_id"
        );
    }
    #endregion

    #region helpers
    #endregion
}
