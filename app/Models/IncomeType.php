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

class IncomeType extends Model
{
    //

    public const META = [
        "label" => "Rodzaje wpłat",
        "icon" => "cash-plus",
        "description" => "Grupy, do których przypisywane są wpłaty.",
        "role" => "archmage",
        "ordering" => 41,
    ];

    use SoftDeletes, Userstamps;

    protected $fillable = [
        "name",
        "desc",
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
            get: fn () => $this->desc,
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
        "desc" => [
            "type" => "TEXT",
            "label" => "Opis",
            "icon" => "text",
        ],
    ];

    public const CONNECTIONS = [
        "transactions" => [
            "model" => MoneyTransaction::class,
            "mode" => "many",
            "readonly" => true,
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

    public function scopeForGigs($query)
    {
        return $query->where("name", "regexp", "granie:");
    }
    #endregion

    #region attributes
    protected function casts(): array
    {
        return [
            "is_expense" => "boolean",
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
    public function transactions()
    {
        return $this->morphMany(MoneyTransaction::class, "typable");
    }
    #endregion

    #region helpers
    #endregion
}
