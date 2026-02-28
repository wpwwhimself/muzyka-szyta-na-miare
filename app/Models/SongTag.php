<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class SongTag extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Tagi utworów",
        "icon" => "tag",
        "description" => "",
        "role" => "archmage",
        "ordering" => 3,
    ];

    protected $fillable = ["name", "icon", "description"];
    public $timestamps = false;

    #region presentation
    public function __toString(): string
    {
        return implode(" ", array_filter([$this->icon, $this->name]));
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
                "icon" => self::META["icon"],
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
            get: fn () => $this->description,
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.ui.button", [
                "icon" => "bullhorn",
                "pop" => "Podglad w katalogu",
                "action" => route("catalog", ["tag" => $this->id]),
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
        "icon" => [
            "type" => "text",
            "label" => "Ikona",
            "icon" => "emoticon",
        ],
        "description" => [
            "type" => "TEXT",
            "label" => "Opis",
            "icon" => "text",
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
        [
            "icon" => "bullhorn",
            "label" => "Podgląd w katalogu",
            "show-on" => "edit",
            "route" => "catalog",
            "params" => ["tag" => "id"],
            "role" => "technical",
        ],
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

    public function scopeForConnection($query)
    {
        return $this->orderByDesc("name");
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
    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
    #endregion

    #region helpers
    #endregion
}
