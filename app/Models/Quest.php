<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Quest extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Zlecenia",
        "icon" => "package-variant-closed",
        "description" => "",
        "role" => "",
        "ordering" => 22,
    ];

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "price_code_override", "price", "paid",
        "status_id",
        "deadline", "hard_deadline", "delayed_payment",
        "wishes",
        "files_ready",
        "has_files_on_external_drive",
    ];

    #region presentation
    public function __toString(): string
    {
        return "[$this->id] " . $this->song->full_title;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->id] " . $this->song->full_title,
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
                "slot" => $this->song->title ?? "Bez tytułu",
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            // get: fn () => view("components.shipyard.app.model.badges", [
            //     "badges" => $this->badges,
            // ])->render(),
            get: fn () => $this->song->artist,
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.quests.details", [
                "quest" => $this,
            ])->render(),
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "price_code_override" => [
            "type" => "text",
            "label" => "Kod wyceny",
            "icon" => "barcode",
        ],
        "price" => [
            "type" => "number",
            "label" => "Cena",
            "icon" => "cash",
            "min" => 0,
            "step" => 0.1,
        ],
        "paid" => [
            "type" => "checkbox",
            "label" => "Opłacone",
            "icon" => "cash-check",
        ],
        "deadline" => [
            "type" => "date",
            "label" => "Termin wykonania",
            "icon" => "calendar-blank",
            "hint" => "Do kiedy najpóźniej jestem w stanie oddać pierwszą wersję utworu.",
        ],
        "hard_deadline" => [
            "type" => "date",
            "label" => "Termin klienta",
            "icon" => "calendar-account",
            "hint" => "Do kiedy klient chciałby najpóźniej otrzymać pliki.",
        ],
        "wishes" => [
            "type" => "TEXT",
            "label" => "Życzenia dot. zlecenia",
            "icon" => "cloud",
            "hint" => "np. transpozycja, czy z linią melodyczną itp.",
        ],
        "delayed_payment" => [
            "type" => "date",
            "label" => "Opóźnienie wpłaty",
            "icon" => "cash-clock",
            "hint" => "Nie wpłacaj przed tym dniem - muszę utrzymać przychody na odpowiednim poziomie z uwagi na zasady działalności nierejestrowanej.",
        ],
        "files_ready" => [
            "type" => "checkbox",
            "label" => "Pliki gotowe",
            "icon" => "file-check",
        ],
        "has_files_on_external_drive" => [
            "type" => "checkbox",
            "label" => "Pliki na dysku istnieją",
            "icon" => "google-drive",
        ],
    ];

    public const CONNECTIONS = [
        "user" => [
            "model" => User::class,
            "mode" => "one",
            "role" => "archmage",
            "field_name" => "client_id",
            "field_label" => "Klient",
        ],
        "song" => [
            "model" => Song::class,
            "mode" => "one",
        ],
        "status" => [
            "model" => Status::class,
            "mode" => "one",
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
        "files_ready" => "boolean",
        "deadline" => "date",
        "hard_deadline" => "date",
        "delayed_payment" => "date",
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
            get: fn ($v) => $this->user->notes->client_name ?? $v,
        );
    }

    public function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->song->full_title,
        );
    }

    // rounded prices
    public function getPriceAttribute($val) {
        return round($val, 2);
    }
    public function setPriceAttribute($val) {
        $this->attributes["price"] = round($val, 2);
    }

    public function getQuestTypeLetterAttribute(){
        return substr($this->id, 0, 1);
    }
    public function getDelayedPaymentInEffectAttribute(){
        return $this->delayed_payment > Carbon::today() && !$this->paid;
    }
    public function getIsPriorityAttribute(){
        return preg_match("/z/", $this->price_code_override);
    }
    public function getLinkToAttribute(){
        return route("quest", ["id" => $this->id]);
    }
    public function getPaymentsSumAttribute(){
        return $this->payments->sum("comment");
    }
    public function getPaymentRemainingAttribute() {
        return $this->price - $this->payments_sum;
    }
    public function getCompletedOnceAttribute() {
        return $this->history->whereIn("new_status_id", [14, 19])->count() > 0;
    }
    #endregion

    #region relations
    public function user(){
        return $this->belongsTo(User::class, "client_id");
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class, "quest_type_letter", "code");
    }
    public function history(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->orderByDesc("date")->orderByDesc("new_status_id");
    }
    public function payments(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->whereIn("new_status_id", [32, 34]);
    }
    public function visibleInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id")->where("visible", true);
    }
    public function allInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id");
    }
    #endregion

    #region helpers
    #endregion
}
