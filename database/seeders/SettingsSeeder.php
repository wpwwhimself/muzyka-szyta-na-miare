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
            ]
        ]);
    }
}
