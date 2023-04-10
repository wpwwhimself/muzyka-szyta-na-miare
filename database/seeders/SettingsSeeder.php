<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("settings")->insert([
            [
                "setting_name" => "veteran_from",
                "desc" => "Po ilu ukończonych zleceniach klient staje się stałym klientem",
                "value_str" => 10
            ],
            [
                "setting_name" => "current_pricing",
                "desc" => "Nazwa obecnego cennika, do którego przypisywani są nowi klienci",
                "value_str" => "B"
            ],
            [
                "setting_name" => "pricing_B_since",
                "desc" => "Od kiedy obowiązuje cennik B",
                "value_str" => "2022-09-09"
            ],
            [
                "setting_name" => "request_expired_after",
                "desc" => "Po ilu dniach Sprzątacz wygasza porzucone zapytania",
                "value_str" => 5
            ],
            [
                "setting_name" => "quest_expired_after",
                "desc" => "Po ilu dniach Sprzątacz wygasza porzucone zlecenia",
                "value_str" => 30
            ],
            [
                "setting_name" => "quest_reminder_time",
                "desc" => "Po ilu dniach Sprzątacz ponawia prośbę o ocenę dla zleceń",
                "value_str" => 5
            ],
            [
                "setting_name" => "workdays_free",
                "desc" => "Które dni tygodnia są traktowane jako wolne i nie są brane pod uwagę przy liczeniu deadline'u",
                "value_str" => "2,5"
            ],
            [
                "setting_name" => "available_days_needed",
                "desc" => "Ile (dostępnych) dni od dziś można proponować deadline",
                "value_str" => 2
            ],
            [
                "setting_name" => "available_day_until",
                "desc" => "Ile maksymalnie zleceń dziennie przyjmuję (dni z tyloma+ zleceniami nie są brane pod uwagę przy liczeniu deadline'u)",
                "value_str" => 3
            ],
            [
                "setting_name" => "work_on_weekends",
                "desc" => "Czy weekendy mają być traktowane jako dni pracujące",
                "value_str" => 0
            ],
            [
                "setting_name" => "safe_old_enough",
                "desc" => "Ile dni sejf musi leżeć odłogiem, żeby był oznaczony jako bezpieczny do usunięcia",
                "value_str" => 120
            ],
        ]);
    }
}
