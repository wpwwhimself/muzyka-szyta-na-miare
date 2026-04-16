<?php

namespace App\Scaffolds;

use App\Models\Request;
use App\Scaffolds\Shipyard\Modal as ShipyardModal;

class Modal extends ShipyardModal
{
    protected static function items(): array
    {
        $contact_form_client_fields = [
            [
                "name" => "client_name",
                "type" => "text",
                "label" => model_field_label("requests", "client_name"),
                "icon" => model_field_icon("requests", "client_name"),
                "required" => true,
            ],
            [
                "name" => "email",
                "type" => "email",
                "label" => model_field_label("requests", "email"),
                "icon" => model_field_icon("requests", "email"),
            ],
            [
                "name" => "phone",
                "type" => "tel",
                "label" => model_field_label("requests", "phone"),
                "icon" => model_field_icon("requests", "phone"),
            ],
            [
                "name" => "other_medium",
                "type" => "text",
                "label" => model_field_label("requests", "other_medium") . " (np. WhatsApp)",
                "icon" => model_field_icon("requests", "other_medium"),
            ],
            [
                "name" => "contact_preference",
                "type" => "select",
                "label" => model_field_label("requests", "contact_preference"),
                "icon" => model_field_icon("requests", "contact_preference"),
                "required" => true,
                "extra" => [
                    "selectData" => model("requests")::getFields()["contact_preference"]["selectData"],
                ],
            ],
        ];
        $contact_form_turing = [
            [
                "name" => "test",
                "type" => "text",
                "label" => "Cztery razy pięć?",
                "icon" => "robot",
                "required" => true,
                "extra" => [
                    "hint" => "To pytanie jest częścią testu antyspamowego. Poprawna odpowiedź jest konieczna do wysłania zapytania.",
                ],
            ],
        ];

        return [
            "add-gig-transaction" => [
                "heading" => "Dodaj wpłatę za granie",
                "target_route" => "gig-transaction.add",
                "fields" => [
                    [
                        "name" => "typable_id",
                        "type" => "select",
                        "label" => "Rodzaj",
                        "icon" => model_icon("quest-types"),
                        "required" => true,
                        "extra" => [
                            "selectData" => [
                                "optionsFromScope" => [
                                    "\App\Models\IncomeType",
                                    "forGigs",
                                    "name",
                                    "id",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => "date",
                        "type" => "date",
                        "label" => "Data",
                        "icon" => model_field_icon("money-transactions", "date"),
                        "required" => true,
                    ],
                    [
                        "name" => "amount",
                        "type" => "number",
                        "label" => "Kwota",
                        "icon" => model_field_icon("money-transactions", "amount"),
                        "required" => true,
                        "extra" => [
                            "step" => 0.01,
                        ],
                    ],
                    [
                        "name" => "description",
                        "type" => "text",
                        "label" => "Opis",
                        "icon" => model_field_icon("money-transactions", "description"),
                    ],
                ],
            ],
            "edit-invoice" => [
                "heading" => "Edycja faktury",
                "target_route" => "invoice-add",
                "fields" => [
                    [
                        "name" => "is_check",
                        "type" => "checkbox",
                        "label" => "Rachunek zamiast faktury",
                        "icon" => "invoice-list-outline",
                    ],
                    [
                        "name" => "quests",
                        "type" => "text",
                        "label" => "Zlecenia",
                        "icon" => model_icon("quests"),
                        "required" => true,
                        "extra" => [
                            "hint" => "Oddzielone spacjami.",
                        ],
                    ],
                    [
                        "type" => "heading",
                        "label" => "Dane nabywcy",
                        "icon" => "cash",
                    ],
                    [
                        "name" => "payer_name",
                        "type" => "text",
                        "label" => "Nazwa",
                        "icon" => "badge-account",
                        "required" => true,
                    ],
                    [
                        "name" => "payer_title",
                        "type" => "text",
                        "label" => "Tytuł",
                        "icon" => "badge-account-horizontal",
                        "extra" => [
                            "hint" => "Mały tekst pod nazwą płatnika.",
                        ],
                    ],
                    [
                        "name" => "payer_address",
                        "type" => "text",
                        "label" => "Adres",
                        "icon" => "map-marker",
                        "required" => true,
                    ],
                    [
                        "name" => "payer_nip",
                        "type" => "text",
                        "label" => "NIP",
                        "icon" => "domain",
                        "required" => true,
                    ],
                    [
                        "name" => "payer_regon",
                        "type" => "text",
                        "label" => "REGON",
                        "icon" => "domain",
                    ],
                    [
                        "name" => "payer_email",
                        "type" => "email",
                        "label" => "E-mail",
                        "icon" => "at",
                    ],
                    [
                        "name" => "payer_phone",
                        "type" => "tel",
                        "label" => "Telefon",
                        "icon" => "phone",
                    ],
                    [
                        "type" => "heading",
                        "label" => "Dane odbiorcy",
                        "icon" => "call-received",
                    ],
                    [
                        "type" => "paragraph",
                        "label" => "Niewymagane",
                        "extra" => [
                            "class" => "ghost",
                        ],
                    ],
                    [
                        "name" => "receiver_name",
                        "type" => "text",
                        "label" => "Nazwa",
                        "icon" => "badge-account",
                    ],
                    [
                        "name" => "receiver_title",
                        "type" => "text",
                        "label" => "Tytuł",
                        "icon" => "badge-account-horizontal",
                    ],
                    [
                        "name" => "receiver_address",
                        "type" => "text",
                        "label" => "Adres",
                        "icon" => "map-marker",
                    ],
                    [
                        "name" => "receiver_nip",
                        "type" => "text",
                        "label" => "NIP",
                        "icon" => "domain",
                    ],
                    [
                        "name" => "receiver_regon",
                        "type" => "text",
                        "label" => "REGON",
                        "icon" => "domain",
                    ],
                    [
                        "name" => "receiver_email",
                        "type" => "email",
                        "label" => "E-mail",
                        "icon" => "at",
                    ],
                    [
                        "name" => "receiver_phone",
                        "type" => "tel",
                        "label" => "Telefon",
                        "icon" => "phone",
                    ],
                ],
            ],
            "mod-cost" => [
                "heading" => "Edycja kosztu",
                "target_route" => "mod-cost",
                "fields" => [
                    [
                        "name" => "created_at",
                        "type" => "date",
                        "label" => "Data",
                        "icon" => "calendar",
                        "required" => true,
                    ],
                    [
                        "name" => "cost_type_id",
                        "type" => "select",
                        "label" => "Typ kosztu",
                        "icon" => "cash-marker",
                        "required" => true,
                        "extra" => [
                            "selectData" => [
                                "optionsFromScope" => [
                                    "\App\Models\CostType",
                                    "forAdding",
                                    "name",
                                    "id",
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => "desc",
                        "type" => "TEXT",
                        "label" => "Opis",
                        "icon" => "text",
                        "required" => true,
                    ],
                    [
                        "name" => "amount",
                        "type" => "number",
                        "label" => "Wartość",
                        "icon" => "cash",
                        "required" => true,
                        "extra" => [
                            "step" => "0.01",
                        ],
                    ],
                ],
            ],
            "pay-for-quest" => [
                "heading" => "Dodaj wpłatę za zlecenie",
                "target_route" => "mod-quest-back",
                "fields" => [
                    [
                        "name" => "comment",
                        "type" => "number",
                        "label" => "Kwota",
                        "icon" => "cash",
                        "required" => true,
                        "extra" => [
                            "step" => 0.01,
                        ],
                    ],
                ],
            ],
            "quest-change-status" => [
                "heading" => "Zmień status zlecenia",
                "target_route" => "mod-quest-back",
                "fields" => [
                    [
                        "name" => "comment",
                        "type" => "TEXT",
                        "label" => "Komentarz (opcjonalnie)",
                        "icon" => "text",
                        "extra" => [
                            "rows" => 10,
                        ],
                    ],
                ],
            ],
            "quest-quote-update" => [
                "heading" => "Zmień wycenę zlecenia",
                "target_route" => "quest-quote-update",
                "summary_route" => "quest-quote-update-summary",
                "fields" => [
                    [
                        "name" => "reason",
                        "type" => "text",
                        "label" => "Zmiana z uwagi na:",
                        "icon" => "chat-question",
                        "required" => true,
                    ],
                    [
                        "name" => "price_code_override",
                        "type" => "text",
                        "label" => "Kod nowej ceny",
                        "icon" => "barcode",
                        "required" => true,
                    ],
                    [
                        "name" => "deadline",
                        "type" => "date",
                        "label" => "Nowy termin",
                        "icon" => "calendar",
                    ],
                ],
            ],
            "restatus-with-comment" => [
                "heading" => "Dodaj komentarz do zmiany statusu",
                "target_route" => "re_quests.restatus-with-comment",
                "fields" => [
                    [
                        "name" => "comment",
                        "type" => "TEXT",
                        "label" => "Komentarz",
                        "icon" => "note",
                    ]
                ],
            ],
            "select-song-to-request" => [
                "heading" => "Przypisz utwór",
                "target_route" => "requests.select-song",
                "fields" => [
                    [
                        "name" => "query",
                        "type" => "lookup",
                        "label" => "Wyszukaj",
                        "icon" => "archive-search",
                        "extra" => [
                            "selectData" => [
                                "dataRoute" => "lookup.songs",
                                "fieldName" => "song_id",
                            ],
                        ]
                    ],
                ]
            ],
            "select-user-to-request" => [
                "heading" => "Przypisz klienta",
                "target_route" => "requests.select-user",
                "fields" => [
                    [
                        "name" => "query",
                        "type" => "lookup",
                        "label" => "Wyszukaj",
                        "icon" => "account-search",
                        "extra" => [
                            "selectData" => [
                                "dataRoute" => "lookup.users",
                                "fieldName" => "client_id",
                            ],
                        ]
                    ]
                ]
            ],
            "send-dj-request" => [
                "heading" => "Wyślij zapytanie",
                "target_route" => "dj-requests.new",
                "fields" => array_merge([
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o sobie",
                        "icon" => "badge-account",
                    ],
                ], $contact_form_client_fields, [
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o zleceniu",
                        "icon" => "music",
                    ],
                    [
                        "name" => "occasion",
                        "type" => "select",
                        "label" => "Rodzaj imprezy",
                        "icon" => "cog-box",
                        "required" => true,
                        "extra" => [
                            "selectData" => [
                                "options" => [
                                    ["label" => "Wesele/zabawa na sali", "value" => "wedding"],
                                    ["label" => "Koncert", "value" => "concert"],
                                    ["label" => "Inne", "value" => "other"],
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => "date",
                        "type" => "datetime-local",
                        "label" => "Termin wydarzenia",
                        "icon" => "calendar",
                        "required" => true,
                    ],
                    [
                        "name" => "wishes",
                        "type" => "TEXT",
                        "label" => "Jakie są Twoje życzenia?",
                        "icon" => "cloud",
                    ],
                ], $contact_form_turing),
            ],
            "send-organista-request" => [
                "heading" => "Wyślij zapytanie",
                "target_route" => "organ-requests.new",
                "fields" => array_merge([
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o sobie",
                        "icon" => "badge-account",
                    ],
                ], $contact_form_client_fields, [
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o zleceniu",
                        "icon" => "music",
                    ],
                    [
                        "name" => "occasion",
                        "type" => "select",
                        "label" => "Rodzaj uroczystości",
                        "icon" => "cog-box",
                        "required" => true,
                        "extra" => [
                            "selectData" => [
                                "options" => [
                                    ["label" => "Msza ślubna", "value" => "wedding"],
                                    ["label" => "Msza jubileuszowa", "value" => "anniversary"],
                                    ["label" => "Pogrzeb", "value" => "funeral"],
                                    ["label" => "Inna uroczystość", "value" => "other"],
                                ],
                            ],
                        ],
                    ],
                    [
                        "name" => "date",
                        "type" => "datetime-local",
                        "label" => "Termin wydarzenia",
                        "icon" => "calendar",
                        "required" => true,
                    ],
                    [
                        "name" => "wishes",
                        "type" => "TEXT",
                        "label" => "Jakie są Twoje życzenia?",
                        "icon" => "cloud",
                    ],
                    [
                        "name" => "equipment",
                        "type" => "checkbox",
                        "label" => "Czy potrzebny jest mój sprzęt?",
                        "icon" => "cog",
                        "extra" => [
                            "hint" => "Mowa tu o nagłośnieniu lub instrumencie",
                        ],
                    ],
                ], $contact_form_turing),
            ],
            "send-podklady-request" => [
                "heading" => "Wyślij zapytanie",
                "target_route" => "requests.new",
                "fields" => array_merge([
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o sobie",
                        "icon" => "badge-account",
                    ],
                ], $contact_form_client_fields, [
                    [
                        "type" => "heading",
                        "label" => "Napisz coś o zleceniu",
                        "icon" => "music"
                    ],
                    [
                        "name" => "quest_type_id",
                        "type" => "select",
                        "label" => "Rodzaj zlecenia",
                        "icon" => "cog-box",
                        "required" => true,
                        "extra" => [
                            "selectData" => [
                                "options" => [
                                    ["label" => "Podkład muzyczny", "value" => 1],
                                    ["label" => "Nuty", "value" => 2],
                                    ["label" => "Obróbka nagrania", "value" => 3],
                                ],
                            ]
                        ],
                    ],
                    [
                        "name" => "title",
                        "type" => "text",
                        "label" => model_field_label("requests", "title"),
                        "icon" => model_field_icon("requests", "title"),
                    ],
                    [
                        "name" => "artist",
                        "type" => "text",
                        "label" => model_field_label("requests", "artist"),
                        "icon" => model_field_icon("requests", "artist"),
                    ],
                    [
                        "name" => "link",
                        "type" => "text",
                        "label" => "Linki do oryginalnych nagrań (oddzielone przecinkami)",
                        "icon" => model_field_icon("requests", "link"),
                    ],
                    [
                        "name" => "wishes",
                        "type" => "TEXT",
                        "label" => "Jakie są Twoje życzenia",
                        "icon" => model_field_icon("requests", "wishes"),
                        "extra" => [
                            "hint" => "np. styl, czy z linią melodyczną itp.",
                        ]
                    ],
                    [
                        "name" => "hard_deadline",
                        "type" => "date",
                        "label" => "Kiedy najpóźniej chcesz otrzymać materiały? (opcj.)",
                        "icon" => model_field_icon("requests", "hard_deadline"),
                    ],
                ], $contact_form_turing),
            ],
            "confirm-quote" => [
                "heading" => "Potwierdzenie wyceny",
                "target_route" => "request-finalize",
                "fields" => [
                    [
                        "type" => "paragraph",
                        "label" => "Zaznacz poniższe zgody, żeby potwierdzić wycenę.",
                    ],
                    [
                        "type" => "paragraph",
                        "label" => "Jeśli nie odpowiada ci któryś z punktów, zamknij to okno i użyj odpowiedniego przycisku na stronie, aby zgłosić uwagi.",
                        "icon" => "alert",
                        "extra" => [
                            "class" => "accent danger",
                        ],
                    ],
                    [
                        "name" => "confirm_song",
                        "type" => "checkbox",
                        "label" => Request::confirmLabels()["confirm_song"],
                        "icon" => model_icon("songs"),
                        "required" => true,
                    ],
                    [
                        "name" => "confirm_price",
                        "type" => "checkbox",
                        "label" => Request::confirmLabels()["confirm_price"],
                        "icon" => model_icon("money-transactions"),
                        "required" => true,
                    ],
                    [
                        "name" => "confirm_delayed_payment",
                        "type" => "checkbox",
                        "label" => Request::confirmLabels()["confirm_delayed_payment"],
                        "icon" => model_field_icon("requests", "delayed_payment"),
                        "required" => true,
                    ],
                    [
                        "name" => "confirm_deadline",
                        "type" => "checkbox",
                        "label" => Request::confirmLabels()["confirm_deadline"],
                        "icon" => model_field_icon("requests", "deadline"),
                        "required" => true,
                    ],
                ],
            ],
        ];
    }
}
