<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Showcase extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Rolki do podkładów",
        "icon" => "bullhorn",
        "description" => "Nagrania wideo, jak brzmią/powstają podkłady.",
        "role" => "archmage",
        "ordering" => 31,
        "defaultSort" => "-date",
    ];

    protected $fillable = [
        "song_id",
        "platform",
        "link",
    ];

    #region presentation
    /**
     * Pretty display of a model - can use components and stuff
     */
    public function __toString(): string
    {
        return $this->song->title;
    }

    /**
     * Display for select options - text only
     */
    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->platform] $this",
        );
    }

    /**
     * Pretty display for model tiles
     */
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

    public function displayPreTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => null,
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
            "icon" => "link",
            "hint" => "Link do nagrania. Embed zostanie wygenerowany automatycznie na froncie na podstawie ID nagrania.",
            "required" => true,
        ],
    ];

    public const CONNECTIONS = [
        "song" => [
            "model" => Song::class,
            "mode" => "one",
            "field_label" => "Powiązany utwór",
        ],
        "platformData" => [
            "model" => ShowcasePlatform::class,
            "mode" => "one",
            "field_name" => "platform",
            "field_label" => "Platforma",
            // "readonly" => true,
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

    /**
     * extended form validation on model save
     * set result to true if everything is ok, false with message to force back with toast
     */
    // public static function validateOnSave($data): array
    // {
    //     $res = [
    //         "result" => true/false,
    //         "message" => "",
    //     ];
    //
    //     // validation...
    //
    //     return $res;
    // }

    /**
     * extended form fields autofill on model save
     * add or update fields inside $data to trigger additional changes based on existing form data
     * then return updated $data
     */
    // public static function autofillOnSave(array $data): array
    // {
    //     return $data;
    // }
    #endregion

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
        //     "type" => "<input type>",
        //     "operator" => "regexp",
        //     "selectData" => [
        //     ],
        // ],
    ];

    public const EXTRA_SECTIONS = [
        // "<id>" => [
        //     "title" => "",
        //     "icon" => "",
        //     "show-on" => "<list|edit>",
        //     "component" => "<component_name>",
        //     "role" => "",
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

    //? override edit button on model list
    // public function modelEditButton(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => view("components.shipyard.ui.button", [
    //             "icon" => "pencil",
    //             "label" => "Edytuj",
    //             "action" => route(...),
    //         ])->render(),
    //     );
    // }
    #endregion

    #region relations
    public function song(){
        return $this->belongsTo(Song::class);
        }

    public function platformData() {
        return $this->belongsTo(ShowcasePlatform::class, "platform", "code");
    }
    #endregion

    #region helpers
    #endregion
}
