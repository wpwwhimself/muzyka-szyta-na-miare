<?php

namespace App\Models;

use App\Models\User;
use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\View\ComponentAttributeBag;
use Laravel\Sanctum\HasApiTokens;
use Mattiverse\Userstamps\Traits\Userstamps;

class UserNote extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = "user_id";

    public const META = [
        "label" => "Notatki o użytkownikach",
        "icon" => "badge-account-horizontal",
        "description" => "Dane kontaktowe klientów, ich preferencje i zobowiązania",
        "role" => "technical",
        "ordering" => 98,
    ];

    use SoftDeletes, Userstamps;

    protected $fillable = [
        "user_id",
        "password",
        "client_name", "email", "phone", "other_medium", "contact_preference",
        "trust", "helped_showcasing", "is_forgotten",
        "budget", "extra_exp",
        "default_wishes", "special_prices",
        "external_drive",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->client_name;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "$this->client_name ($this->email)",
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
                "slot" => $this->client_name,
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
        "client_name" => [
            "type" => "text",
            "label" => "Imię i nazwisko",
            "icon" => "badge-account",
            "required" => true,
        ],
        "password" => [
            "type" => "text",
            "label" => "Hasło",
            "icon" => "key",
            "required" => true,
        ],
        "email" => [
            "type" => "email",
            "label" => "Email",
            "icon" => "email",
        ],
        "phone" => [
            "type" => "tel",
            "label" => "Numer telefonu",
            "icon" => "phone",
        ],
        "other_medium" => [
            "type" => "text",
            "label" => "Inna forma kontaktu",
            "icon" => "human-greeting-proximity",
            "hint" => "np. WhatsApp",
        ],
        "trust" => [
            "type" => "select",
            "label" => "Zaufanie",
            "icon" => "heart",
            "selectData" => [
                "options" => [
                    ["value" => 0, "label" => "neutralne"],
                    ["value" => 1, "label" => "zaufany"],
                    ["value" => 2, "label" => "ulubiony"],
                    ["value" => -1, "label" => "krętacz i oszust"],
                ],
            ],
        ],
        "budget" => [
            "type" => "number",
            "label" => "Budżet",
            "icon" => "safe",
            "hint" => "Kwota nadpłat, wykorzystywana na poczet przyszłych zleceń.",
            "min" => 0,
            "step" => 0.01,
        ],
        "extra_exp" => [
            "type" => "number",
            "label" => "Dodatkowe doświadczenie",
            "icon" => "folder-arrow-up",
            "hint" => "Liczba ukończonych zleceń, które nie są zarejestrowane w systemie. Jest dodawana do całkowitej liczby zleceń i decyduje o statusie weterana.",
            "min" => 0,
        ],
        "default_wishes" => [
            "type" => "TEXT",
            "label" => "Domyślne życzenia",
            "icon" => "cloud",
        ],
        "special_prices" => [
            "type" => "TEXT",
            "label" => "Specjalne warunki cenowe",
            "icon" => "account-cash",
        ],
        "external_drive" => [
            "type" => "url",
            "label" => "Link do chmury",
            "icon" => "google-drive",
        ],
        "is_forgotten" => [
            "type" => "checkbox",
            "label" => "Zapomniany",
            "icon" => "ghost",
            "hint" => "Od dawna nie ma kontaktu z klientem. Nie jest brany pod uwagę podczas przeliczania saturacji przychodów.",
        ],
        "helped_showcasing" => [
            "type" => "select",
            "label" => "Status patrona",
            "icon" => "seal",
            "selectData" => [
                "options" => [
                    ["value" => 0, "label" => "brak"],
                    ["value" => 1, "label" => "oczekuje"],
                    ["value" => 2, "label" => "potwierdzony"],
                ],
            ],
        ],
    ];

    public const CONNECTIONS = [
        "user" => [
            "model" => User::class,
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
        //     "mode" => "<one|many>",
        //     "operator" => "",
        //     "options" => [
        //         "<label>" => <value>,
        //     ],
        // ],
    ];

    #region scopes
    use HasStandardScopes;

    public function scopeClients($query)
    {
        return $query->whereNotIn("user_id", [0, 1]);
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
        "pickiness",
    ];

    use HasStandardAttributes;

    public function badges(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                "veteran" => [
                    "condition" => $this->is_veteran,
                    "icon" => "shield-account",
                    "label" => "Stały klient"
                ],
                "patron" => [
                    "condition" => $this->is_patron && is_archmage(),
                    "icon" => "seal",
                    "class" => "showcase-highlight",
                    "label" => "Patron"
                ],
                "trusted" => [
                    "condition" => $this->trust > 0,
                    "icon" => "hand-heart",
                    "class" => "accent success",
                    "label" => "Zaufany"
                ],
                "favourite" => [
                    "condition" => $this->is_favourite,
                    "icon" => "heart",
                    "class" => "accent success",
                    "label" => "Ulubiony"
                ],
                "active" => [
                    "condition" => $this->user->top10->where("type", "active")->count() > 0,
                    "icon" => "chart-line",
                    "class" => "accent success",
                    "label" => "Zleceń w ostatnich 3 mc: ".$this->user->questsRecent()->count()
                ],
                "picky" => [
                    "condition" => $this->pickiness >= 1.5 && is_archmage(),
                    "icon" => "fencing",
                    "class" => "accent error",
                    "label" => "Wybredny"
                ],
                "forgotten" => [
                    "condition" => $this->is_forgotten && is_archmage(),
                    "icon" => "ghost",
                    "class" => "accent success",
                    "label" => "Zapomniany"
                ],
                "kio" => [
                    "condition" => $this->trust < 0 && is_archmage(),
                    "icon" => "ninja",
                    "class" => "accent error",
                    "label" => "Na czarnej liście"
                ],
                "special_prices" => [
                    "condition" => $this->special_prices && is_archmage(),
                    "icon" => "file-sign",
                    "label" => "Niestandardowe ceny:<br>"._ct_($this->special_prices)
                ],
                "default_wishes" => [
                    "condition" => $this->default_wishes && is_archmage(),
                    "icon" => "cloud",
                    "label" => "Domyślne życzenia:<br>"._ct_($this->default_wishes)
                ],
                "budget" => [
                    "condition" => $this->budget && is_archmage(),
                    "icon" => "safe-square",
                    "class" => "accent success",
                    "label" => "Budżet:<br>"._c_(as_pln($this->budget))
                ],
            ],
        );
    }

    public function exp(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user->questsDone->count() + $this->extra_exp,
        );
    }

    public function isWoman(): Attribute
    {
        return Attribute::make(
            get: fn () => substr(explode(" ", $this->client_name)[0], -1) == "a",
        );
    }

    public function isOld(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at < BEGINNING(),
        );
    }

    public function isVeteran(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->exp >= VETERAN_FROM(),
        );
    }

    public function isPatron(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->helped_showcasing == 2,
        );
    }

    public function pickiness(): Attribute
    {
        return Attribute::make(
            get: function () {
                $correction_requests = StatusChange::where("changed_by", $this->id)->whereIn("new_status_id", [16, 26])->count();
                $quests_total = $this->user->quests->count();
                if($quests_total == 0) return 0;
                return round($correction_requests / $quests_total, 2);
            }
        );
    }

    public function canSeeFiles(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->trust >= 0,
        );
    }

    public function upcomingQuestsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user->quests->whereNotIn("status_id", [17, 18, 19])->count(),
        );
    }

    public function isFavourite(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->trust >= 2,
        );
    }
    #endregion

    #region relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    #endregion
}
