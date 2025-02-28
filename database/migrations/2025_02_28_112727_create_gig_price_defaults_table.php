<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGigPriceDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gig_price_defaults', function (Blueprint $table) {
            $table->string("name")->primary();
            $table->string("label");
            $table->string("category");
            $table->float("value");
            $table->timestamps();
        });

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gig_price_defaults');
    }
}
