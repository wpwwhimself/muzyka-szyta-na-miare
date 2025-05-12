<?php

use App\Models\GigPriceDefault;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateGigPriceDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("gig_price_defaults")->truncate();
        DB::table("gig_price_defaults")->insert([
            ["category" => "drive", "name" => "travel_distance_km", "label" => "Odległość w jedną stronę [km]", "value" => 30],
            ["category" => "drive", "name" => "fuel_cost_pln_per_l", "label" => "Koszt paliwa [zł/l]", "value" => 6.75],
            ["category" => "drive", "name" => "fuel_consumption_l_per_100_km", "label" => "Zuzycie paliwa [l/100km]", "value" => 6.5],
            ["category" => "show", "name" => "gig_time_buffer_h", "label" => "Bufor czasu przed graniem [h]", "value" => 0.75],
            ["category" => "show", "name" => "gig_duration_h", "label" => "Czas grania [h]", "value" => 1],
            ["category" => "show", "name" => "my_gear_surcharge", "label" => "Dopłata za własny sprzęt [zł]", "value" => 100],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("gig_price_defaults")->truncate();
        DB::table("gig_price_defaults")->insert([
            ["category" => "time", "name" => "gig_duration_h", "label" => "Czas grania [h]", "value" => 1],
            ["category" => "time", "name" => "travel_time_h", "label" => "Czas przejazdu [h]", "value" => 0.5],
            ["category" => "time", "name" => "gig_time_buffer_h", "label" => "Bufor czasu [h]", "value" => 0.5],
            ["category" => "distance", "name" => "travel_distance_km", "label" => "Odległość [km]", "value" => 30],
            ["category" => "distance", "name" => "fuel_cost_pln_per_l", "label" => "Koszt paliwa [zł/l]", "value" => 6.75],
            ["category" => "distance", "name" => "fuel_consumption_l_per_100_km", "label" => "Zuzycie paliwa [l/100km]", "value" => 6.5],
            ["category" => "gain", "name" => "gain_net", "label" => "Oczekiwany zysk [zł]", "value" => 150],
            ["category" => "gain", "name" => "gain_per_h", "label" => "Koszt rbh [zł/h]", "value" => 60],
        ]);
    }
}
