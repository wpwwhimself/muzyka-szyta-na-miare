<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Request extends Model
{
    use HasFactory, Uuids;

    public const META = [
        "label" => "Zapytania",
        "icon" => "chat",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $fillable = [
        "made_by_me",
        "client_id", "client_name", "email", "phone", "other_medium", "contact_preference",
        "song_id", "quest_type_id", "title", "artist", "link", "genre_id", "wishes", "wishes_quest",
        "price_code", "price", "deadline", "hard_deadline", "delayed_payment",
        "status_id", "quest_id"
    ];

    #region presentation
    public function __toString(): string
    {
        return "[$this->id] " . $this->song->full_title;
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
                "icon" => $this->quest_type->icon ?? self::META["icon"],
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                ]),
                "slot" => $this->song?->title ?? $this->title ?? "Bez tytułu",
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            // get: fn () => view("components.shipyard.app.model.badges", [
            //     "badges" => $this->badges,
            // ])->render(),
            get: fn () => $this->song?->artist ?? $this->artist,
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.requests.details", [
                "request" => $this,
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
        "title" => [
            "type" => "text",
            "label" => "Tytuł utworu",
            "icon" => "music-box",
            "required" => true,
        ],
        "artist" => [
            "type" => "text",
            "label" => "Wykonawca",
            "icon" => "account-music"
        ],
        "link" => [
            "type" => "text",
            "label" => "Linki do utworu",
            "icon" => "link",
            "hint" => "Oddzielone przecinkami",
        ],
        "wishes" => [
            "type" => "TEXT",
            "label" => "Życzenia dot. utworu",
            "icon" => "cloud",
            "hint" => "np. styl itp.",
        ],
        "wishes_quest" => [
            "type" => "TEXT",
            "label" => "Życzenia dot. zlecenia",
            "icon" => "cloud",
            "hint" => "np. transpozycja, czy z linią melodyczną itp.",
        ],
    ];

    public const CONNECTIONS = [
        "user" => [
            "model" => User::class,
            "mode" => "one",
            "role" => "archmage",
            // "field_name" => "",
            "field_label" => "Klient",
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
    protected $casts = [
        "deadline" => "datetime",
        "hard_deadline" => "datetime",
        "delayed_payment" => "datetime",
    ];

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

    public function clientName(): Attribute
    {
        return Attribute::make(
            get: fn ($v) => $this->user?->notes->client_name ?? $v,
        );
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($v) => $v !== null ? round($v, 2) : null,
            set: fn ($v) => $v !== null ? round($v, 2) : null,
        );
    }

    public function getIsPriorityAttribute(){
        return preg_match("/z/", $this->price_code);
    }
    public function getLinkToAttribute(){
        return route("request", ["id" => $this->id]);
    }
    public function getFullTitleAttribute(){
        return implode(' – ', array_filter([
            $this->artist,
            $this->title ?? 'utwór bez tytułu',
        ], fn($v) => !empty($v)));
    }
    #endregion

    #region relations
    public function user(){
        return $this->belongsTo(User::class, "client_id");
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
    public function history(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->orderByDesc("date")->orderByDesc("new_status_id");
    }
    public function quest(){
        return $this->belongsTo(Quest::class);
    }
    #endregion

    #region helpers
    #endregion
}
