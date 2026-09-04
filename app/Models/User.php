<?php

namespace App\Models;

use App\Http\Controllers\JanitorController;
use Wpwwhimself\Shipyard\Models\Role;
use Wpwwhimself\Shipyard\Models\User as ShipyardUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends ShipyardUser
{
    public const FROM_SHIPYARD = true;

    public const META = [
        "label" => "Użytkownicy",
        "icon" => "account",
        "description" => "Lista użytkowników systemu. Każdy z wymienionych może otrzymać role, które nadają mu uprawnienia do korzystania z konkretnych funkcjonalności.",
        "role" => "",
        "uneditable" => [
            "archmage",
        ],
        "uneditableField" => "display_name",
        "defaultSort" => "-exp",
    ];

    #region presentation
    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->id] $this",
        );
    }

    public function nameAndBadges(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(" ", [
                $this,
                $this->display_subtitle,
            ]),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("shipyard::components.stats.counter", [
                "rank" => $this->exp,
                "label" => "Zaakceptowane zlecenia",
                "style" => "military",
            ])->render()
            . view("shipyard::components.app.model.badges", [
                "badges" => $this->badges,
            ])->render(),
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("shipyard::components.app.model.fields-preview", [
                    "model" => $this,
                    "fields" => [
                        "email",
                        "phone",
                        "other_medium",
                    ],
                ])->render(),
        );
    }

    public function modelEditButton(): Attribute
    {
        return Attribute::make(
            get: fn () => view("shipyard::components.ui.button", [
                "icon" => "email",
                "pop" => "Wyślij maila",
                "action" => route("client-mail-prepare", ["client_id" => $this->id]),
            ])->render()
            . view("shipyard::components.ui.button", [
                "icon" => "pencil",
                "label" => "Edytuj",
                "action" => route("admin.model.edit", ["model" => "users", "id" => $this->id]),
            ])->render(),
        );
    }

    public function profileComponents(): Attribute
    {
        $dashboardData = [
            "requests" => Request::with(["song"])
                ->whereNotIn("status_id", [4, 7, 8, 9])
                ->orderBy("updated_at"),
            "quests_ongoing" => Quest::with(["song", "quest_type", "status"])
                ->whereIn("status_id", STATUSES_WAITING_FOR_ME())
                ->orderByRaw("case status_id when 13 then 1 else 0 end")
                ->orderByRaw("case when deadline is null then 1 else 0 end")
                ->orderByRaw("case status_id
                    when 12 then 1
                    when 11 or 14 or 16 or 21 or 26 or 96 then 5
                    else 99
                end")
                ->orderByRaw("case when deadline <= now() + interval 1 day then 0 else 1 end")
                ->orderByRaw("case when hard_deadline is not null and hard_deadline < deadline then hard_deadline else deadline end")
                ->orderByRaw("case when price_code_override regexp 'z' and status_id in (11, 12, 16, 26, 96) then 0 else 1 end")
                ->orderByRaw("paid desc")
                ->orderBy("created_at"),
            "quests_review" => Quest::with(["song", "quest_type", "status"])
                ->whereNotIn("status_id", [17, 18, 19])
                ->whereNotIn("status_id", STATUSES_WAITING_FOR_ME())
                ->orderByDesc("deadline")
                ->orderBy("created_at"),
        ];

        if ($this->hasRole("client", true)) {
            $dashboardData["requests"] = $dashboardData["requests"]->where("client_id", $this->id);
            $dashboardData["quests_ongoing"] = $dashboardData["quests_ongoing"]->where("client_id", $this->id);
            $dashboardData["quests_review"] = $dashboardData["quests_review"]->where("client_id", $this->id);

            $dashboardData["quests_total"] = $this->exp;
            $dashboardData["unpaids"] = $this->questsUnpaid()->get();
        } else {
            $dashboardData["recent"] = StatusChange::whereNotIn("new_status_id", [9, 32, 34])
                ->orderByDesc("date")
                ->limit(25)
                ->get()
                ->map(function ($change) {
                    $change->is_request = is_request($change->re_quest_id);
                    return $change;
                });
            $dashboardData["patrons_adepts"] = User::where("helped_showcasing", 1)->get();
            $dashboardData["showcases_missing"] = Quest::where("status_id", 19)
                ->whereDate("updated_at", ">", Carbon::today()->subWeeks(2))
                ->get()
                ->filter(fn($q) => !$q->song->has_showcase_file && $q->quest_type?->code == "P");

            $dashboardData["janitor_log"] = collect(json_decode(Storage::get("janitor_log.json")) ?? [])
                ->map(function ($i) {
                    // translating subjects
                    $length = strlen($i->subject);
                    $replacement =
                        ($length == 36) ? Request::find($i->subject)
                        : (($length == 6) ? Quest::find($i->subject)
                        : Song::find($i->subject));
                    $i->subject = $replacement ?? $i->subject;

                    // translating operations
                    if(in_array($i->comment, array_keys(JanitorController::$OPERATIONS))){
                        [$status_id, $comment_code] = explode("_", $i->comment);
                        $i->comment = [
                            "status_id" => $status_id,
                            "comment" => JanitorController::$OPERATIONS[$i->comment],
                        ];
                    }

                    return $i;
                });
        }
        $dashboardData["requests"] = $dashboardData["requests"]->get();
        $dashboardData["quests_ongoing"] = $dashboardData["quests_ongoing"]->get();
        $dashboardData["quests_review"] = $dashboardData["quests_review"]->get();

        return Attribute::make(
            get: fn () => [
                view("components.dashboard", [
                    "user" => $this,
                    "data" => $dashboardData,
                ])->render(),
            ],
        );
    }
    #endregion

    #region fields
    public const FIELDS = [
        "name" => [
            "type" => "text",
            "label" => "Login",
            "icon" => "badge-account-outline",
            "disabled" => true,
        ],
        "display_name" => [
            "type" => "text",
            "label" => "Imię i nazwisko",
            "icon" => "badge-account",
            "required" => true,
        ],
        "password_actual" => [
            "type" => "dummy-text",
            "label" => "Hasło",
            "icon" => "key",
            "disabled" => true,
        ],
        "email" => [
            "type" => "email",
            "label" => "Email",
            "icon" => "email",
            "required" => true,
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
        "contact_preference" => [
            "type" => "select",
            "label" => "Preferowana forma kontaktu",
            "icon" => "card-account-phone",
            "selectData" => [
                "options" => [
                    ["value" => "email", "label" => "email"],
                    ["value" => "sms", "label" => "sms/komunikator"],
                ],
            ],
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
            "role" => "archmage",
        ],
        "budget" => [
            "type" => "number",
            "label" => "Budżet",
            "icon" => "safe-square",
            "hint" => "Kwota nadpłat, wykorzystywana na poczet przyszłych zleceń.",
            "min" => 0,
            "step" => 0.01,
            "role" => "archmage",
        ],
        "extra_exp" => [
            "type" => "number",
            "label" => "Dodatkowe doświadczenie",
            "icon" => "folder-arrow-up",
            "hint" => "Liczba ukończonych zleceń, które nie są zarejestrowane w systemie. Jest dodawana do całkowitej liczby zleceń i decyduje o statusie weterana.",
            "min" => 0,
            "role" => "archmage",
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
            "role" => "archmage",
        ],
        "external_drive" => [
            "type" => "url",
            "label" => "Link do chmury",
            "icon" => "google-drive",
            "role" => "archmage",
        ],
        "is_forgotten" => [
            "type" => "checkbox",
            "label" => "Zapomniany",
            "icon" => "ghost",
            "hint" => "Od dawna nie ma kontaktu z klientem. Nie jest brany pod uwagę podczas przeliczania saturacji przychodów.",
            "role" => "archmage",
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
            "role" => "archmage",
        ],
        "invoice_data" => [
            "type" => "JSON",
            "label" => "Dane do faktury",
            "icon" => "invoice-list",
            "hint" => "Lista dostępnych pól:<ul>
                <li>payer_name - nazwa płatnika</li>
                <li>payer_title - tytuł płatnika</li>
                <li>payer_address - adres</li>
                <li>payer_nip - NIP</li>
                <li>payer_regon - REGON</li>
                <li>payer_email - email</li>
                <li>payer_phone - telefon</li>
                <li>...te same pola z prefiksem `receiver` dotyczą danych odbiorcy i są opcjonalne</li>
            </ul>",
            "columnTypes" => [
                "Pole" => "text",
                "Wartość" => "text",
            ],
        ],
    ];

    protected $fillable = [
        "name",
        "display_name",
        "email",
        "password",
        "roles",
        "p13n",
        "password_actual",
        "phone", "other_medium", "contact_preference",
        "trust", "helped_showcasing", "is_forgotten",
        "budget", "extra_exp",
        "default_wishes", "special_prices",
        "external_drive",
        "invoice_data",
    ];

    public const CONNECTIONS = [
        "quests" => [
            "model" => Quest::class,
            "mode" => "many-reverse",
            "role" => "archmage",
        ],
    ];

    public const ACTIONS = [
        // disabled password reset
    ];
    #endregion

    #region sorts and filters
    public const SORTS = [
        "name" => [
            "label" => "nazwisko",
            "compare-using" => "field",
            "discr" => "display_name",
        ],
        "exp" => [
            "label" => "doświadczenie",
            "compare-using" => "function",
            "discr" => "exp",
        ],
        // "<name>" => [
        //     "label" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        // ],
    ];

    public const FILTERS = [
        "name" => [
            "label" => "Nazwisko",
            "icon" => "account-badge",
            "compare-using" => "field",
            "discr" => "display_name",
            "type" => "text",
            "operator" => "regexp",
        ],
        "email" => [
            "label" => "Email",
            "icon" => "at",
            "compare-using" => "field",
            "discr" => "email",
            "type" => "email",
            "operator" => "regexp",
        ],
        "phone" => [
            "label" => "Telefon",
            "icon" => "phone",
            "compare-using" => "field",
            "discr" => "phone",
            "type" => "text",
            "operator" => "regexp",
        ],
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
    #endregion

    #region scopes
    public function scopeForConnection($query)
    {
        return $this->orderBy("id");
    }

    public function scopeClients($query)
    {
        return $query->whereNotIn("id", [0, 1]);
    }

    public function scopeMailableClients($query)
    {
        return $query->clients()->where("email", "not regexp", "@test");
    }
    #endregion

    #region attributes and helpers
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'p13n' => "collection",
            "invoice_data" => "json",
        ];
    }

    protected $appends = [
        "pickiness",
    ];

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
                    "condition" => $this->top10->where("type", "active")->count() > 0,
                    "icon" => "chart-line",
                    "class" => "accent success",
                    "label" => "Zleceń w ostatnich 3 mc: ".$this->questsRecent()->count()
                ],
                "early_payer" => [
                    "condition" => $this->likes_to_pay_early && is_archmage(),
                    "icon" => "cash-fast",
                    "class" => "accent success",
                    "label" => "Lubi płacić przed odbiorem",
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

    public function canBeMailed(): Attribute
    {
        return Attribute::make(
            get: fn () => !Str::contains($this->email, "@test"),
        );
    }

    public function exp(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->questsDone->count() + $this->extra_exp,
        );
    }

    public function isWoman(): Attribute
    {
        return Attribute::make(
            get: fn () => substr(explode(" ", $this->display_name)[0], -1) == "a",
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

    public function likesToPayEarly(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rate = $this->questsDone->map(function ($q) {
                    $date_accepted = $q->history->firstWhere("new_status_id", 19)?->date;
                    return ($date_accepted)
                        ? (int) $q->payments->first()?->date->lt($date_accepted)
                        : 0;
                })->avg() ?? 0;
                return $rate > 0.5;
            }
        );
    }

    public function pickiness(): Attribute
    {
        return Attribute::make(
            get: function () {
                $correction_requests = StatusChange::where("changed_by", $this->id)->whereIn("new_status_id", [16, 26])->count();
                $quests_total = $this->quests->count();
                if($quests_total == 0) return 0;
                return round($correction_requests / $quests_total, 2);
            }
        );
    }
    public function pickinessPretty(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->pickiness * 100) . "%",
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
            get: fn () => $this->quests->whereNotIn("status_id", [17, 18, 19])->count(),
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
    public function quests(){
        return $this->hasMany(Quest::class, "client_id");
    }

    public function questsDone(){
        return $this->quests()
            ->where("status_id", 19);
    }

    public function questsUnpaid(){
        return $this->quests()
            ->where("paid", 0)
            ->whereNotIn("status_id", [18]);
    }

    public function questsRecent() {
        return $this->quests()
            ->whereDate("updated_at", ">=", Carbon::today()->subMonths(3));
    }

    public function comments() {
        return $this->hasMany(StatusChange::class, "changed_by")
            ->whereIn("new_status_id", [14, 19])
            ->whereNotNull("comment")
            ->orderByDesc("date");
    }

    public function top10() {
        return $this->morphMany(Top10::class, "entity");
    }
    #endregion
}
