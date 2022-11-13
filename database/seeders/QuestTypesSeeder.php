<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("quest_types")->insert([
            [
                "code" => "P", "type" => "podkład muzyczny",
                "fa_symbol" => "fa-file-audio"
            ],
            [
                "code" => "N", "type" => "nuty",
                "fa_symbol" => "fa-music"
            ],
            [
                "code" => "W", "type" => "występ",
                "fa_symbol" => "fa-guitar"
            ],
            [
                "code" => "O", "type" => "obróbka",
                "fa_symbol" => "fa-screwdriver-wrench"
            ],
        ]);
    }
}
