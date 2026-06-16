<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class Invoice extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Faktury i rachunki",
        "icon" => "invoice-list",
        "description" => "",
        "role" => "",
        "ordering" => 43,
        "defaultSort" => "-id",
    ];

    protected $fillable = [
        "full_code_override",
        "visible",
        "is_check",
        "amount", "paid",
        "payer_name", "payer_title", "payer_address", "payer_nip", "payer_regon", "payer_email", "payer_phone",
        "receiver_name", "receiver_title", "receiver_address", "receiver_nip", "receiver_regon", "receiver_email", "receiver_phone",
        "ksef_number", "ksef_link",
    ];

    #region presentation
    public function __toString(): string
    {
        return $this->full_code;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_code,
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
                "slot" => $this->full_code,
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
            get: fn () => view("components.shipyard.app.values-preview", [
                "data" => [
                    [
                        "icon" => "cash",
                        "label" => "Nabywca",
                        "value" => $this->payer,
                    ],
                    [
                        "icon" => "call-received",
                        "label" => "Odbiorca",
                        "slot" => $this->receiver,
                    ],
                ],
                "hideEmpty" => true,
            ])->render(),
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "visible" => [
            "type" => "checkbox",
            "label" => "Widoczna",
            "icon" => "eye",
        ],
        "full_code_override" => [
            "type" => "text",
            "label" => "Własny numer faktury",
            "icon" => "pound",
        ],
        "ksef_number" => [
            "type" => "text",
            "label" => "Numer KSeF",
            "icon" => "barcode",
        ],
        "ksef_link" => [
            "type" => "link",
            "label" => "Link do KSeF",
            "icon" => "link",
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
            "icon" => "eye",
            "label" => "Podgląd dokumentu",
            "show-on" => "edit",
            "route" => "invoice",
            "role" => "",
            // "dangerous" => true,
        ],
    ];
    #endregion

    // use CanBeSorted;
    public const SORTS = [
        "id" => [
            "label" => "Numer",
            "compare-using" => "field",
            "discr" => "id",
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

    //? override edit button on model list
    public function modelEditButton(): Attribute
    {
        return Attribute::make(
            get: fn () => implode("", [
                view("components.shipyard.ui.button", [
                    "icon" => "eye",
                    "label" => "Podgląd",
                    "action" => route("invoice", ["id" => $this->id]),
                ])->render(),
                view("components.shipyard.ui.button", [
                    "icon" => "pencil",
                    "label" => "Edytuj",
                    "action" => route("admin.model.edit", ["model" => "invoices", "id" => $this->id]),
                ])->render(),
            ]),
        );
    }

    public function getFullCodeAttribute(){
        return $this->full_code_override ?? implode("/", [
            $this->id,
            ($this->is_check ? "R" : "F"),
            $this->created_at->format("y"),
        ]);
    }

    public function getIsPaidAttribute(){
        return $this->amount == $this->paid;
    }

    public function payer(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(" ", array_filter([
                $this->payer_name,
                $this->payer_title,
            ])),
        );
    }

    public function receiver(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(" ", array_filter([
                $this->receiver_name,
                $this->receiver_title,
            ])),
        );
    }
    #endregion

    #region relations
    public function quests(){
        return $this->belongsToMany(Quest::class, InvoiceQuest::class)->withPivot(["primary", "amount", "paid"]);
    }
    #endregion

    #region helpers
    #endregion
}
