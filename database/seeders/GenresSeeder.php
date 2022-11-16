<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("genres")->insert([
            ["name" => "szybka przeróbka"],
            ["name" => "songwriter"],
            ["name" => "gitary"],
            ["name" => "gitary + elektro"],
            ["name" => "gitary + orkiestra"],
            ["name" => "elektro"],
            ["name" => "orkiestra"],
            ["name" => "jazz"],
            ["name" => "ludowe"],
        ]);
    }
}
