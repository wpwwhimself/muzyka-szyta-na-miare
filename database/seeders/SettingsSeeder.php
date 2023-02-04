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
                "value_str" => 10
            ],
            [
                "setting_name" => "current_pricing",
                "value_str" => "B"
            ],
            [
                "setting_name" => "pricing_B_since",
                "value_str" => "2022-09-09"
            ],
            [
                "setting_name" => "request_expired_after",
                "value_str" => 5
            ],
            [
                "setting_name" => "quest_expired_after",
                "value_str" => 30
            ],
            [
                "setting_name" => "quest_reminder_time",
                "value_str" => 5
            ],
            [
                "setting_name" => "workdays_free",
                "value_str" => "2,5"
            ],
            [
                "setting_name" => "available_days_needed",
                "value_str" => 2
            ],
            [
                "setting_name" => "available_day_until",
                "value_str" => 3
            ],
        ]);
    }
}
