<?php

use App\Models\Shipyard\NavItem;
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
        NavItem::insert([
            [
                "name" => "Pulpit",
                "visible" => 1,
                "order" => 1,
                "icon" => "home-account",
                "target_type" => 1,
                "target_name" => "dashboard",
            ],
            [
                "name" => "Zapytania",
                "visible" => 1,
                "order" => 2,
                "icon" => model_icon("requests"),
                "target_type" => 1,
                "target_name" => "requests",
            ],
            [
                "name" => "Zlecenia",
                "visible" => 1,
                "order" => 3,
                "icon" => model_icon("quests"),
                "target_type" => 1,
                "target_name" => "quests",
            ],
            [
                "name" => "Cennik",
                "visible" => 1,
                "order" => 4,
                "icon" => model_icon("prices"),
                "target_type" => 1,
                "target_name" => "prices",
            ],
            [
                "name" => "Utwory",
                "visible" => 1,
                "order" => 5,
                "icon" => model_icon("songs"),
                "target_type" => 1,
                "target_name" => "songs",
            ],
            [
                "name" => "Klienci",
                "visible" => 1,
                "order" => 6,
                "icon" => model_icon("users"),
                "target_type" => 1,
                "target_name" => "clients",
            ],
            [
                "name" => "Finanse",
                "visible" => 1,
                "order" => 7,
                "icon" => "bank",
                "target_type" => 1,
                "target_name" => "finance",
            ],
            [
                "name" => "Reklama",
                "visible" => 1,
                "order" => 8,
                "icon" => "bullhorn",
                "target_type" => 1,
                "target_name" => "showcases",
            ],
            [
                "name" => "DJ",
                "visible" => 1,
                "order" => 9,
                "icon" => "headphones",
                "target_type" => 1,
                "target_name" => "dj",
            ],
            [
                "name" => "Statystyki",
                "visible" => 1,
                "order" => 10,
                "icon" => "finance",
                "target_type" => 1,
                "target_name" => "stats",
            ],
        ]);

        NavItem::all()->each(function ($item) {
            if (in_array($item->name, [
                "Klienci",
                "Finanse",
                "Reklama",
                "DJ",
                "Statystyki",
            ])) return;

            $item->roles()->attach("client");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        NavItem::whereIn("target_name", [

        ])->delete();
    }
};
