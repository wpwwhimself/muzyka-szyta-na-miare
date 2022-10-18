<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("statuses")->insert([
            [
                "id" => 1,
                "status_name" => "nowe"
            ],
            [
                "id" => 5,
                "status_name" => "wycena do akceptacji"
            ],
            [
                "id" => 6,
                "status_name" => "wycena zakwestionowana"
            ],
            [
                "id" => 7,
                "status_name" => "nie podejmę się"
            ],
            [
                "id" => 8,
                "status_name" => "wycena odrzucona"
            ],
            [
                "id" => 9,
                "status_name" => "przyjęte"
            ],
            [
                "id" => 11,
                "status_name" => "nowe"
            ],
            [
                "id" => 12,
                "status_name" => "prace w toku"
            ],
            [
                "id" => 13,
                "status_name" => "prace przerwane"
            ],
            [
                "id" => 15,
                "status_name" => "czeka na recenzję"
            ],
            [
                "id" => 16,
                "status_name" => "oddane do poprawki"
            ],
            [
                "id" => 18,
                "status_name" => "odrzucone"
            ],
            [
                "id" => 19,
                "status_name" => "zaakceptowane"
            ],
            [
                "id" => 26,
                "status_name" => "oddane do poprawki po terminie"
            ],
            [
                "id" => 120,
                "status_name" => "analiza i wstępna obróbka"
            ],
            [
                "id" => 121,
                "status_name" => "nagrania: perkusja"
            ],
            [
                "id" => 122,
                "status_name" => "nagrania: gitary"
            ],
            [
                "id" => 123,
                "status_name" => "nagrania: fortepiany"
            ],
            [
                "id" => 124,
                "status_name" => "nagrania: syntezatory"
            ],
            [
                "id" => 125,
                "status_name" => "nagrania: dęte"
            ],
            [
                "id" => 129,
                "status_name" => "mix i mastering"
            ]
        ]);
    }
}
