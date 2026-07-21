<?php

namespace App\Models;

use Wpwwhimself\Shipyard\Traits\HasStandardAttributes;
use Wpwwhimself\Shipyard\Traits\HasStandardFields;
use Wpwwhimself\Shipyard\Traits\HasStandardScopes;
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
        "track_ready",
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
            get: fn () => view("shipyard::components.app.h", [
                "lvl" => 3,
                "icon" => $this->icon ?? self::META["icon"],
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                    "style" => "color: $this->color;",
                ]),
                "slot" => $this->option_label,
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("shipyard::components.app.model.badges", [
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
            get: fn () => view("shipyard::components.app.model.connections-preview", [
                "connections" => self::getConnections(),
                "model" => $this,
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
        "track_ready" => [
            "type" => "checkbox",
            "label" => "Podkład gotowy",
            "icon" => "disc",
        ]
    ];

    public const CONNECTIONS = [
        "genre" => [
            "model" => Genre::class,
            "mode" => "one",
            // "field_name" => "",
            "field_label" => "Gatunek",
        ],
        "compositions" => [
            "model" => Composition::class,
            "mode" => "many-reverse",
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
                "options" => self::TEMPOS,
                "emptyOption" => "Wszystkie",
            ],
        ],
        "ready" => [
            "label" => "Gotowe do grania",
            "icon" => "disc",
            "compare-using" => "field",
            "discr" => "track_ready",
            "type" => "select",
            "operator" => "=",
            "selectData" => [
                "options" => [
                    ["label" => "Tak", "value" => 1],
                    ["label" => "Nie", "value" => 0],
                ],
                "emptyOption" => "Wszystkie",
            ],
        ],
    ];

    public const EXTRA_SECTIONS = [
        // "compositions_list" => [
        //     "title" => "Lista utworów",
        //     "icon" => "music",
        //     "show-on" => "edit",
        //     "component" => "dj.set-compositions-list",
        //     "role" => "archmage",
        // ],
    ];

    #region scopes
    use HasStandardScopes;

    public function scopeVisible($query)
    {
        return $query;
    }

    public function scopeReady()
    {
        return $this->where("track_ready", 1);
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

    public function badges(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                [
                    "label" => "Podkład niegotowy",
                    "icon" => "disc-alert",
                    "class" => "accent error",
                    // "style" => "",
                    "condition" => !$this->track_ready,
                ],
                // [
                //     "html" => "",
                // ],
            ],
        );
    }

    public function icon(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(self::TEMPOS)->firstWhere("value", $this->id[1])["icon"],
        );
    }

    public function color(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(self::TEMPOS)->firstWhere("value", $this->id[1])["color"],
        );
    }

    //? override add button on model list
    public static function modelAddButton(): string
    {
        return view("shipyard::components.ui.button", [
            "icon" => "plus",
            "pop" => "Dodaj",
            "action" => "none",
            "attributes" => new ComponentAttributeBag([
                "onclick" => "openModal('add-dj-set')",
                "class" => "primary",
            ]),
        ])->render();
    }

    //? override edit button on model list
    // public function modelEditButton(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => view("shipyard::components.ui.button", [
    //             "icon" => "pencil",
    //             "label" => "Edytuj",
    //             "action" => route(...),
    //         ])->render(),
    //     );
    // }
    #endregion

    #region relations
    public function compositions()
    {
        return $this->hasMany(Composition::class);
    }
    #endregion

    #region helpers
    const TEMPOS = [
        ["label" => "Bujane", "value" => "B", "icon" => "tortoise", "color" => "dodgerblue"],
        ["label" => "Wolne", "value" => "W", "icon" => "horse-variant", "color" => "limegreen"],
        ["label" => "Szybkie", "value" => "S", "icon" => "rabbit", "color" => "orange"],
        ["label" => "Padaka", "value" => "P", "icon" => "fire", "color" => "red"],
    ];

    public static function nextSetId(string $tempo_code): string
    {
        if (!in_array($tempo_code, array_map(fn ($t) => $t["value"], self::TEMPOS))) {
            throw new \Exception("Niepoprawny kod tempa.");
        }
        $letter = strtoupper($tempo_code);
        $newest_id = self::where("id", "like", "G$letter%")->orderBy("id", "desc")->value("id") ?? ("G" . $letter . "0");
        $newest_id_last = substr($newest_id, 2);
        if(in_array($newest_id_last, ["0", "Z"])){
            return "G" . $letter . "0";
        }
        return "G" . $letter . to_base36(from_base36($newest_id_last) + 1, 1);
    }
    #endregion
}
