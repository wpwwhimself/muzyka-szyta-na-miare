<?php

namespace App\Models;

use App\Models\Shipyard\Setting as ShipyardSetting;

class Setting extends ShipyardSetting
{
    public const FROM_SHIPYARD = true;

    public static function fields(): array
    {
        /**
         * * hierarchical structure of the page *
         * grouped by sections (title, subtitle, icon, identifier)
         * each section contains fields (name, label, hint, icon)
         */
        return [
            [
                "title" => "Kalendarz pracy",
                "subtitle" => "Kiedy i jak często pracuję",
                "icon" => "calendar",
                "id" => "calendar",
                "fields" => [
                    [
                        "name" => "msznm_available_day_until",
                        "label" => "Ile maksymalnie zleceń dziennie przyjmuję",
                        "hint" => "Dni z tyloma+ zleceniami nie są brane pod uwagę przy liczeniu deadline'u; Od niedzieli, po przecinku.",
                        "icon" => "calendar-range",
                    ],
                    [
                        "name" => "msznm_available_days_needed",
                        "label" => "Ile (dostępnych) dni od dziś można proponować deadline",
                        "icon" => "calendar-clock",
                    ],
                    [
                        "name" => "msznm_work_on_weekends",
                        "label" => "Czy weekendy mają być traktowane jako dni pracujące",
                        "icon" => "calendar-weekend",
                    ],
                ],
            ],
            [
                "title" => "Finanse",
                "icon" => "cash",
                "id" => "finances",
                "fields" => [
                    [
                        "name" => "msznm_current_pricing",
                        "label" => "Nazwa obecnego cennika, do którego przypisywani są nowi klienci",
                        "icon" => "tag",
                        "selectData" => [
                            "options" => [
                                ["label" => "A", "value" => "A"],
                                ["label" => "B", "value" => "B"],
                            ],
                        ],
                    ],
                    [
                        "name" => "msznm_pricing_B_since",
                        "label" => "Od kiedy obowiązuje cennik B",
                        "icon" => "tag-check",
                    ],
                    [
                        "name" => "msznm_min_account_balance",
                        "label" => "Minimalne saldo konta (na potrzeby obliczania wypłaty)",
                        "icon" => "abacus",
                    ],
                    [
                        "name" => "msznm_quest_minimal_price",
                        "label" => "Minimalna cena zlecenia dla poszczególnych typów zleceń: kolejno P, N, O",
                        "icon" => "cash",
                    ],
                ],
            ],
            [
                "title" => "Sprzątacz",
                "icon" => "broom",
                "id" => "janitor",
                "fields" => [
                    [
                        "name" => "msznm_quest_expired_after",
                        "label" => "Po ilu dniach Sprzątacz wygasza porzucone zlecenia (opłacone wygaszane są 2× szybciej)",
                        "icon" => "timer-sand",
                    ],
                    [
                        "name" => "msznm_quest_reminder_time",
                        "label" => "Po ilu dniach Sprzątacz ponawia prośbę o ocenę dla zleceń",
                        "icon" => "timer-sand",
                    ],
                    [
                        "name" => "msznm_request_expired_after",
                        "label" => "Po ilu dniach Sprzątacz wygasza porzucone zapytania",
                        "icon" => "timer-sand",
                    ],
                    [
                        "name" => "msznm_safe_old_enough",
                        "label" => "Ile dni sejf musi leżeć odłogiem, żeby był oznaczony jako bezpieczny do usunięcia",
                        "icon" => "timer-sand",
                    ],
                ],
            ],
            [
                "title" => "Klienci",
                "icon" => "account-tie",
                "id" => "clients",
                "fields" => [
                    [
                        "name" => "msznm_veteran_from",
                        "label" => "Po ilu ukończonych zleceniach klient staje się stałym klientem",
                        "icon" => "shield-account",
                    ],
                ],
            ],
        ];
    }

}
