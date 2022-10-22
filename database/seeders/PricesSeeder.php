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
                "price_a" => 35,
                "price_b" => 50
            ],
            [
                "indicator" => "c",
                "service" => "podkład typowy",
                "price_a" => 50,
                "price_b" => 70
            ],
            [
                "indicator" => "d",
                "service" => "kompozycja od podstaw",
                "price_a" => 150,
                "price_b" => 250
            ],
            [
                "indicator" => "s",
                "service" => "przeróbka gotowego podkładu",
                "price_a" => 15,
                "price_b" => 20
            ],
            [
                "indicator" => "a",
                "service" => "przearanżowanie podkładu",
                "price_a" => 15,
                "price_b" => 20
            ],
            [
                "indicator" => "x",
                "service" => "podkład trudny",
                "price_a" => 0.3,
                "price_b" => 0.3
            ],
            [
                "indicator" => "o",
                "service" => "dopłata post-terminowa (%)",
                "price_a" => 0.3,
                "price_b" => 0.3
            ],
            [
                "indicator" => "z",
                "service" => "obsługa poza kolejnością (%)",
                "price_a" => 1,
                "price_b" => 1
            ],
            [
                "indicator" => "h",
                "service" => "akordy/przepisanie melodii",
                "price_a" => 30,
                "price_b" => 30
            ],
            [
                "indicator" => "i",
                "service" => "transkrypcja",
                "price_a" => 140,
                "price_b" => 140
            ],
            [
                "indicator" => "j",
                "service" => "aranż",
                "price_a" => 200,
                "price_b" => 200
            ],
            [
                "indicator" => "k",
                "service" => "kompozycja",
                "price_a" => 340,
                "price_b" => 340
            ],
            [
                "indicator" => "u",
                "service" => "dopłata za partię ponad 4. (%)",
                "price_a" => 0.07,
                "price_b" => 0.07
            ],
            [
                "indicator" => "n",
                "service" => "nagrania do własnego podkładu",
                "price_a" => 25,
                "price_b" => 100
            ],
            [
                "indicator" => "p",
                "service" => "nagrania do mojego podkładu",
                "price_a" => 0,
                "price_b" => 0
            ],
            [
                "indicator" => "q",
                "service" => "przygotowanie filmu",
                "price_a" => 20,
                "price_b" => 40
            ],
            [
                "indicator" => "r",
                "service" => "przygotowanie napisów do filmu",
                "price_a" => 20,
                "price_b" => 20
            ],
            [
                "indicator" => "v",
                "service" => "występ jako akompaniator (godz.)",
                "price_a" => 60,
                "price_b" => 200
            ],
            [
                "indicator" => "w",
                "service" => "występ w zespole (godz.)",
                "price_a" => 80,
                "price_b" => 300
            ]
        ]);
    }
}
