<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Cost extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Koszty",
        "icon" => "cash-minus",
        "description" => "Koszty związane z działalnością - na sprzęt, outsourcing, rachunki itp.",
        "role" => "archmage",
        "ordering" => 99,
    ];

    protected $fillable = [
        "cost_type_id",
        "desc",
        "amount",
        "created_at",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->type;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type,
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
                "slot" => $this->type,
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
        "desc" => [
            "type" => "TEXT",
            "label" => "Opis",
            "icon" => "text",
        ],
        "amount" => [
            "type" => "number",
            "label" => "Wartość",
            "icon" => "cash",
            "step" => 0.01,
        ],
        "created_at" => [
            "type" => "date",
            "label" => "Data transakcji",
            "icon" => "calendar",
            "required" => true,
        ],
    ];

    public const CONNECTIONS = [
        "type" => [
            "model" => CostType::class,
            "mode" => "one",
            // "field_name" => "",
            "field_label" => "Typ",
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
        //     "mode" => "<one|many>",
        //     "operator" => "",
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
            //
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
    public function type(){
        return $this->belongsTo(CostType::class, "cost_type_id");
    }
    #endregion

    #region helpers
    #endregion
}
