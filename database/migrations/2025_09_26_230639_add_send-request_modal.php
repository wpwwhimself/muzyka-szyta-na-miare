<?php

use App\Models\Shipyard\Modal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Modal::create([
            "name" => "send-podklady-request",
            "visible" => 2,
            "heading" => "Wyślij zapytanie",
            "target_route" => "requests.new",
            "fields" => [
                [
                    null,
                    "heading",
                    "Napisz coś o sobie",
                    "badge-account",
                    false,
                ],
                [
                    "client_name",
                    "text",
                    "Imię i nazwisko",
                    "badge-account",
                    true,
                ],
                [
                    "email",
                    "email",
                    "Adres email",
                    "email",
                    false,
                ],
                [
                    "phone",
                    "tel",
                    "Numer telefonu",
                    "phone",
                    false,
                ],
                [
                    "other_medium",
                    "text",
                    "Inna forma kontaktu (np. Whatsapp)",
                    "human-greeting-proximity",
                    false,
                ],
                [
                    "contact_preference",
                    "select",
                    "Preferowana forma kontaktu",
                    "card-account-phone",
                    false,
                    [
                        "selectData" => [
                            "options" => [
                                ["value" => "email", "label" => "email"],
                                ["value" => "sms", "label" => "sms/komunikator"],
                            ],
                        ],
                    ],
                ],
                [
                    null,
                    "heading",
                    "Napisz coś o zleceniu",
                    "music",
                    false,
                ],
                [
                    "quest_type_id",
                    "select",
                    "Rodzaj zlecenia",
                    "cog-box",
                    true,
                    json_encode([
                        "selectData" => [
                            "options" => [
                                ["label" => "Podkład muzyczny", "value" => 1],
                                ["label" => "Nuty", "value" => 2],
                                ["label" => "Obróbka nagrania", "value" => 3],
                            ],
                        ],
                    ]),
                ],
                [
                    "title",
                    "text",
                    "Tytuł utworu",
                    "music-box",
                    true,
                ],
                [
                    "artist",
                    "text",
                    "Wykonawca",
                    "account-music",
                    false,
                ],
                [
                    "link",
                    "text",
                    "Linki do oryginalnych nagrań (oddzielone przecinkami)",
                    "link",
                    false,
                ],
                [
                    "wishes",
                    "TEXT",
                    "Jakie są Twoje życzenia?",
                    "cloud",
                    false,
                    json_encode([
                        "hint" => "np. styl, czy z linią melodyczną itp.",
                    ]),
                ],
                [
                    "hard_deadline",
                    "date",
                    "Kiedy najpóźniej chcesz otrzymać materiały? (opcjonalnie)",
                    "timer-sand",
                    false,
                ],
                [
                    "test",
                    "text",
                    "Cztery razy pięć?",
                    "robot",
                    true,
                    [
                        "hint" => "To pytanie jest częścią testu antyspamowego. Poprawna odpowiedź jest konieczna do wysłania zapytania.",
                    ],
                ],
            ],
        ]);

        Modal::create([
            "name" => "send-organista-request",
            "visible" => 2,
            "heading" => "Wyślij zapytanie",
            "target_route" => "organ-requests.new",
            "fields" => [
                [
                    null,
                    "heading",
                    "Napisz coś o sobie",
                    "badge-account",
                    false,
                ],
                [
                    "client_name",
                    "text",
                    "Imię i nazwisko",
                    "badge-account",
                    true,
                ],
                [
                    "email",
                    "email",
                    "Adres email",
                    "email",
                    false,
                ],
                [
                    "phone",
                    "tel",
                    "Numer telefonu",
                    "phone",
                    false,
                ],
                [
                    "other_medium",
                    "text",
                    "Inna forma kontaktu (np. Whatsapp)",
                    "human-greeting-proximity",
                    false,
                ],
                [
                    "contact_preference",
                    "select",
                    "Preferowana forma kontaktu",
                    "card-account-phone",
                    false,
                    [
                        "selectData" => [
                            "options" => [
                                ["value" => "email", "label" => "email"],
                                ["value" => "sms", "label" => "sms/komunikator"],
                            ],
                        ],
                    ],
                ],
                [
                    null,
                    "heading",
                    "Napisz coś o zleceniu",
                    "music",
                    false,
                ],
                [
                    "occasion",
                    "select",
                    "Rodzaj uroczystości",
                    "cog-box",
                    true,
                    json_encode([
                        "selectData" => [
                            "options" => [
                                ["label" => "Msza ślubna", "value" => "wedding"],
                                ["label" => "Msza jubileuszowa", "value" => "anniversary"],
                                ["label" => "Pogrzeb", "value" => "funeral"],
                                ["label" => "Inna uroczystość", "value" => "other"],
                            ],
                        ],
                    ]),
                ],
                [
                    "date",
                    "datetime-local",
                    "Termin wydarzenia",
                    "calendar",
                    true,
                ],
                [
                    "wishes",
                    "TEXT",
                    "Jakie są Twoje życzenia?",
                    "cloud",
                    false,
                ],
                [
                    "equipment",
                    "checkbox",
                    "Czy potrzebny jest mój sprzęt?",
                    "cog",
                    false,
                    json_encode([
                        "hint" => "Mowa tu o nagłośnieniu lub instrumencie",
                    ]),
                ],
                [
                    "test",
                    "text",
                    "Cztery razy pięć?",
                    "robot",
                    true,
                    [
                        "hint" => "To pytanie jest częścią testu antyspamowego. Poprawna odpowiedź jest konieczna do wysłania zapytania.",
                    ],
                ],
            ],
        ]);

        Modal::create([
            "name" => "send-dj-request",
            "visible" => 2,
            "heading" => "Wyślij zapytanie",
            "target_route" => "dj-requests.new",
            "fields" => [
                [
                    null,
                    "heading",
                    "Napisz coś o sobie",
                    "badge-account",
                    false,
                ],
                [
                    "client_name",
                    "text",
                    "Imię i nazwisko",
                    "badge-account",
                    true,
                ],
                [
                    "email",
                    "email",
                    "Adres email",
                    "email",
                    false,
                ],
                [
                    "phone",
                    "tel",
                    "Numer telefonu",
                    "phone",
                    false,
                ],
                [
                    "other_medium",
                    "text",
                    "Inna forma kontaktu (np. Whatsapp)",
                    "human-greeting-proximity",
                    false,
                ],
                [
                    "contact_preference",
                    "select",
                    "Preferowana forma kontaktu",
                    "card-account-phone",
                    false,
                    [
                        "selectData" => [
                            "options" => [
                                ["value" => "email", "label" => "email"],
                                ["value" => "sms", "label" => "sms/komunikator"],
                            ],
                        ],
                    ],
                ],
                [
                    null,
                    "heading",
                    "Napisz coś o zleceniu",
                    "music",
                    false,
                ],
                [
                    "occasion",
                    "select",
                    "Rodzaj imprezy",
                    "cog-box",
                    true,
                    json_encode([
                        "selectData" => [
                            "options" => [
                                ["label" => "Wesele/zabawa na sali", "value" => "wedding"],
                                ["label" => "Koncert", "value" => "concert"],
                                ["label" => "Inne", "value" => "other"],
                            ],
                        ],
                    ]),
                ],
                [
                    "date",
                    "datetime-local",
                    "Termin wydarzenia",
                    "calendar",
                    true,
                ],
                [
                    "wishes",
                    "TEXT",
                    "Jakie są Twoje życzenia?",
                    "cloud",
                    false,
                ],
                [
                    "test",
                    "text",
                    "Cztery razy pięć?",
                    "robot",
                    true,
                    [
                        "hint" => "To pytanie jest częścią testu antyspamowego. Poprawna odpowiedź jest konieczna do wysłania zapytania.",
                    ],
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::whereIn("name", [
            "send-podklady-request",
            "send-organista-request",
            "send-dj-request",
        ])->delete();
    }
};
