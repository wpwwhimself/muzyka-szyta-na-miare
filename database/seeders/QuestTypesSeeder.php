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
            ["code" => "P", "type" => "podkład muzyczny"],
            ["code" => "N", "type" => "nuty"],
            ["code" => "W", "type" => "występ"]
        ]);
    }
}
