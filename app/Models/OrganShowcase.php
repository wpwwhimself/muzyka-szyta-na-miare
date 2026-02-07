<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class OrganShowcase extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Rolki organowe",
        "icon" => "piano",
        "description" => "Nagrania wideo, jak brzmi moje granie na organach.",
        "role" => "archmage",
        "ordering" => 32,
        "defaultSort" => "-date",
    ];

    protected $fillable = [
        "platform",
        "link",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->platform] $this",
        );
    }

    public function displayTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.h", [
                "lvl" => 3,
                "icon" => $this->platformData->name,
                "iconMode" => "url",
                "iconData" => $this->platformData->icon_url,
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                ]),
                "slot" => $this,
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
        "link" => [
            "type" => "url",
            "label" => "Link do rolki",
            "hint" => "Link do nagrania. Embed zostanie wygenerowany automatycznie na froncie na podstawie ID nagrania.",
            "icon" => "link",
            "required" => true,
        ],
    ];

    public const CONNECTIONS = [
        "platformData" => [
            "model" => ShowcasePlatform::class,
            "mode" => "one",
            "field_name" => "platform",
            "field_label" => "Platforma",
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
        "date" => [
            "label" => "Data utworzenia",
            "compare-using" => "field",
            "discr" => "created_at",
        ],
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
    public function platformData() {
        return $this->belongsTo(ShowcasePlatform::class, "platform", "code");
    }
    #endregion

    #region helpers
    #endregion
}
