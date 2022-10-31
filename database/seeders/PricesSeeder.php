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
                "indicator" => "b",
                "service" => "podkład kameralny",
                "operation" => "+",
                "price_a" => 35,
                "price_b" => 50
            ],
            [
                "indicator" => "c",
                "service" => "podkład typowy",
                "operation" => "+",
                "price_a" => 50,
                "price_b" => 70
            ],
            [
                "indicator" => "d",
                "service" => "kompozycja od podstaw",
                "operation" => "+",
                "price_a" => 150,
                "price_b" => 250
            ],
            [
                "indicator" => "s",
                "service" => "przeróbka gotowego podkładu",
                "operation" => "+",
                "price_a" => 15,
                "price_b" => 20
            ],
            [
                "indicator" => "a",
                "service" => "przearanżowanie podkładu",
                "operation" => "+",
                "price_a" => 15,
                "price_b" => 25
            ],
            [
                "indicator" => "x",
                "service" => "podkład trudny",
                "operation" => "*",
                "price_a" => 0.3,
                "price_b" => 0.3
            ],
            [
                "indicator" => "o",
                "service" => "dopłata post-terminowa",
                "operation" => "*",
                "price_a" => 0.3,
                "price_b" => 0.3
            ],
            [
                "indicator" => "z",
                "service" => "obsługa poza kolejnością",
                "operation" => "*",
                "price_a" => 1,
                "price_b" => 1
            ],
            [
                "indicator" => "h",
                "service" => "akordy/przepisanie melodii",
                "operation" => "*",
                "price_a" => 30,
                "price_b" => 30
            ],
            [
                "indicator" => "i",
                "service" => "transkrypcja",
                "operation" => "+",
                "price_a" => 140,
                "price_b" => 140
            ],
            [
                "indicator" => "j",
                "service" => "aranż",
                "operation" => "+",
                "price_a" => 200,
                "price_b" => 200
            ],
            [
                "indicator" => "k",
                "service" => "kompozycja",
                "operation" => "+",
                "price_a" => 340,
                "price_b" => 340
            ],
            [
                "indicator" => "u",
                "service" => "dopłata za partię ponad 4.",
                "operation" => "*",
                "price_a" => 0.07,
                "price_b" => 0.07
            ],
            [
                "indicator" => "n",
                "service" => "nagrania do własnego podkładu",
                "operation" => "+",
                "price_a" => 25,
                "price_b" => 100
            ],
            [
                "indicator" => "p",
                "service" => "nagrania do mojego podkładu",
                "operation" => "+",
                "price_a" => 0,
                "price_b" => 0
            ],
            [
                "indicator" => "q",
                "service" => "przygotowanie filmu",
                "operation" => "+",
                "price_a" => 20,
                "price_b" => 60
            ],
            [
                "indicator" => "r",
                "service" => "przygotowanie napisów do filmu",
                "operation" => "+",
                "price_a" => 20,
                "price_b" => 60
            ],
            [
                "indicator" => "v",
                "service" => "występ jako akompaniator (godz.)",
                "operation" => "+",
                "price_a" => 60,
                "price_b" => 250
            ],
            [
                "indicator" => "w",
                "service" => "występ w zespole (godz.)",
                "operation" => "+",
                "price_a" => 80,
                "price_b" => 300
            ]
        ]);
    }
}
