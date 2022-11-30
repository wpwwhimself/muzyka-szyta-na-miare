<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("prices")->insert([
            [
                "indicator" => "b", "service" => "podkład kameralny", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 35, "price_b" => 50
            ],
            [
                "indicator" => "c", "service" => "podkład typowy", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 50, "price_b" => 70
            ],
            [
                "indicator" => "d", "service" => "podkład skomponowany od podstaw", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 150, "price_b" => 250
            ],
            [
                "indicator" => "s", "service" => "przeróbka gotowego podkładu", "quest_type_id" => 3,
                "operation" => "+", "price_a" => 15, "price_b" => 20
            ],
            [
                "indicator" => "a", "service" => "przearanżowanie podkładu", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 15, "price_b" => 25
            ],
            [
                "indicator" => "x", "service" => "wysoka trudność", "quest_type_id" => null,
                "operation" => "*", "price_a" => 0.3, "price_b" => 0.3
            ],
            [
                "indicator" => "o", "service" => "prace ponad miesiąc po terminie", "quest_type_id" => null,
                "operation" => "*", "price_a" => 0.3, "price_b" => 0.3
            ],
            [
                "indicator" => "y", "service" => "do 3 kolejnych wersji więcej", "quest_type_id" => null,
                "operation" => "+", "price_a" => 10, "price_b" => 15
            ],
            [
                "indicator" => "z", "service" => "obsługa poza kolejnością", "quest_type_id" => null,
                "operation" => "*", "price_a" => 1, "price_b" => 1
            ],
            [
                "indicator" => "h", "service" => "akordy/przepisanie melodii", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 30, "price_b" => 60
            ],
            [
                "indicator" => "i", "service" => "transkrypcja", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 140, "price_b" => 140
            ],
            [
                "indicator" => "j", "service" => "aranż", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 200, "price_b" => 200
            ],
            [
                "indicator" => "k", "service" => "kompozycja", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 340, "price_b" => 340
            ],
            [
                "indicator" => "u", "service" => "dopłata za każdą partię ponad 4.", "quest_type_id" => 2,
                "operation" => "*", "price_a" => 0.07, "price_b" => 0.07
            ],
            [
                "indicator" => "n", "service" => "nagrania do własnego podkładu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 25, "price_b" => 100
            ],
            [
                "indicator" => "p", "service" => "nagrania do mojego podkładu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 0, "price_b" => 0
            ],
            [
                "indicator" => "q", "service" => "przygotowanie filmu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 20, "price_b" => 60
            ],
            [
                "indicator" => "r", "service" => "przygotowanie napisów do filmu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 20, "price_b" => 60
            ],
            [
                "indicator" => "v", "service" => "występ jako akompaniator (godz.)", "quest_type_id" => null,
                "operation" => "+", "price_a" => 60, "price_b" => 250
            ],
            [
                "indicator" => "w", "service" => "występ w zespole (godz.)", "quest_type_id" => null,
                "operation" => "+", "price_a" => 80, "price_b" => 300
            ],
            [
                "indicator" => "g", "service" => "mixing i mastering", "quest_type_id" => 3,
                "operation" => "+", "price_a" => 50, "price_b" => 50
            ],
            /* zniżki specjalne -- zawsze na końcu */
            [
                "indicator" => "=", "service" => "zniżka stałego klienta", "quest_type_id" => null,
                "operation" => "*", "price_a" => -0.3, "price_b" => -0.15
            ],
            [
                "indicator" => "-", "service" => "zniżka za opinię", "quest_type_id" => null,
                "operation" => "*", "price_a" => -0.05, "price_b" => -0.05
            ],
        ]);
    }
}
