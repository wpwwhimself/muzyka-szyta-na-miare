<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as ContractsAuditable;

class DjSet extends Model implements ContractsAuditable
{
    //

    public const META = [
        "label" => "Zestawy DJa",
        "icon" => "dance-ballroom",
        "description" => "Zestawy kompozycji, pozwalające grać na imprezie",
        "role" => "archmage",
        // "checkOwnerUnless" => "", // for roles above, allow to see only one's own objects unless they're also other role
        "ordering" => 71,
        // "listScope" => "", // default scope to list items in model editor, empty defaults to forAdminList
        "defaultSort" => "id", // default sort, as it appears in url
        // "defaultFltr" => "", // default filters //todo expand
    ];

    public $incrementing = false;
    public $keyType = "string";

    use Auditable;

    protected $fillable = [
        "id",
        "name",
        "genre_id",
        "compositions",
    ];

    #region presentation
    /**
     * Pretty display of a model - can use components and stuff
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Display for select options - text only
     */
    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->id] $this->name",
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
                "icon" => $this->icon ?? self::META["icon"],
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                ]),
                "slot" => $this->option_label,
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
            get: fn () => view("components.shipyard.app.icon-label-value", [
                "icon" => "counter",
                "label" => "Liczba utworów",
                "slot" => $this->compositions->count(),
            ])->render(),
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "id" => [
            "type" => "text",
            "label" => "ID",
            "icon" => "database",
            "required" => true,
            "hint" => "Format: <code>G{kod_tempa}{lp}</code>, kody tempa: Bujane, Wolne, Szybkie, Padaka",
        ],
    ];

    public const CONNECTIONS = [
        "genre" => [
            "model" => Genre::class,
            "mode" => "one",
            // "field_name" => "",
            "field_label" => "Gatunek",
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
    public static function validateOnSave($data): array
    {
        $res = [
            "result" => true,
            "message" => "",
        ];
    
        if ($data["id"][0] !== "G") {
            $res = [
                "result" => false,
                "message" => "ID musi zaczynać się od G.",
            ];
        }
    
        return $res;
    }

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
        "id" => [
            "label" => "ID",
            "compare-using" => "field",
            "discr" => "id",
        ],
    ];

    public const FILTERS = [
        "tempo" => [
            "label" => "Tempo",
            "icon" => "metronome",
            "compare-using" => "field",
            "discr" => "id",
            "type" => "select",
            "operator" => "regexp",
            "selectData" => [
                "options" => [
                    ["label" => "Bujane", "value" => "^GB"],
                    ["label" => "Wolne", "value" => "^GW"],
                    ["label" => "Szybkie", "value" => "^GS"],
                    ["label" => "Padaka", "value" => "^GP"],
                ],
                "emptyOption" => "Wszystkie",
            ],
        ],
    ];

    public const EXTRA_SECTIONS = [
        "compositions_list" => [
            "title" => "Lista utworów",
            "icon" => "music",
            "show-on" => "edit",
            "component" => "dj.set-compositions-list",
            "role" => "archmage",
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

    ];

    use HasStandardAttributes;

    public function compositions(): Attribute
    {
        return Attribute::make(
            get: fn ($v) => collect(explode(",", $v ?? ""))
                ->filter(fn ($id) => !empty($id))
                ->map(fn ($id) => Composition::find($id)),
            set: fn ($v) => $v,
        );
    }

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

    //? override add button on model list
    // public static function modelAddButton(): string
    // {
    //     return view("components.shipyard.ui.button", [
    //         "icon" => "plus",
    //         "label" => "Dodaj",
    //         "action" => route(...),
    //         "class" => "primary",
    //     ])->render();
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
    #endregion

    #region helpers
    #endregion
}
