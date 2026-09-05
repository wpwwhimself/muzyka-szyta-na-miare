<?php

use App\Models\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            [
                "indicator" => "b", "service" => "podkład kameralny", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 35, "price_b" => 50, "price_c" => 60,
            ],
            [
                "indicator" => "c", "service" => "podkład typowy", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 50, "price_b" => 70, "price_c" => 80
            ],
            [
                "indicator" => "d", "service" => "podkład skomponowany od podstaw", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 150, "price_b" => 250, "price_c" => 250
            ],
            [
                "indicator" => "s", "service" => "przeróbka gotowego podkładu", "quest_type_id" => 3,
                "operation" => "+", "price_a" => 15, "price_b" => 20, "price_c" => 30
            ],
            [
                "indicator" => "a", "service" => "przearanżowanie podkładu", "quest_type_id" => 1,
                "operation" => "+", "price_a" => 15, "price_b" => 25, "price_c" => 30
            ],
            [
                "indicator" => "x", "service" => "wysoka trudność", "quest_type_id" => null,
                "operation" => "*", "price_a" => 0.3, "price_b" => 0.3, "price_c" => 0.3
            ],
            [
                "indicator" => "o", "service" => "prace ponad miesiąc po terminie", "quest_type_id" => null,
                "operation" => "*", "price_a" => 0.3, "price_b" => 0.3, "price_c" => 0.3
            ],
            [
                "indicator" => "y", "service" => "do 3 kolejnych wersji więcej", "quest_type_id" => null,
                "operation" => "+", "price_a" => 10, "price_b" => 15, "price_c" => 20
            ],
            [
                "indicator" => "z", "service" => "obsługa poza kolejnością", "quest_type_id" => null,
                "operation" => "*", "price_a" => 1, "price_b" => 1, "price_c" => 1
            ],
            [
                "indicator" => "h", "service" => "akordy/przepisanie melodii", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 30, "price_b" => 60, "price_c" => 50
            ],
            [
                "indicator" => "i", "service" => "transkrypcja", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 140, "price_b" => 140, "price_c" => 150
            ],
            [
                "indicator" => "j", "service" => "aranż", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 200, "price_b" => 200, "price_c" => 200
            ],
            [
                "indicator" => "k", "service" => "kompozycja", "quest_type_id" => 2,
                "operation" => "+", "price_a" => 340, "price_b" => 340, "price_c" => 340
            ],
            [
                "indicator" => "u", "service" => "do 4 kolejnych partii więcej", "quest_type_id" => 2,
                "operation" => "*", "price_a" => 0.07, "price_b" => 0.07, "price_c" => 0.07
            ],
            [
                "indicator" => "q", "service" => "przygotowanie filmu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 20, "price_b" => 60, "price_c" => 80
            ],
            [
                "indicator" => "r", "service" => "przygotowanie napisów do filmu", "quest_type_id" => null,
                "operation" => "+", "price_a" => 20, "price_b" => 60, "price_c" => 80
            ],
            [
                "indicator" => "g", "service" => "mixing i mastering", "quest_type_id" => 3,
                "operation" => "+", "price_a" => 50, "price_b" => 50, "price_c" => 75
            ],
            /* zniżki specjalne -- zawsze na końcu */
            [
                "indicator" => "=", "service" => "zniżka stałego klienta", "quest_type_id" => null,
                "operation" => "*", "price_a" => -0.3, "price_b" => -0.15, "price_c" => -0.15
            ],
            [
                "indicator" => "-", "service" => "zniżka za opinię", "quest_type_id" => null,
                "operation" => "*", "price_a" => -0.05, "price_b" => -0.05, "price_c" => -0.05
            ],
            [
                "indicator" => "#", "service" => "zniżka za przetarty szlak", "quest_type_id" => null,
                "operation" => "*", "price_a" => -0.75, "price_b" => -0.75, "price_c" => -0.5
            ],
        ] as $p) {
            if (Price::where("indicator", $p["indicator"])->exists()) continue;
            Price::insert($p);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
