<?php

use App\Models\Status;
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
        Schema::table('statuses', function (Blueprint $table) {
            $table->string("color")->nullable();
        });

        foreach ([
            1 => "rgb(245, 174, 185)",
            11 => "rgb(245, 174, 185)",
            12 => "rgb(50, 127, 226)",
            13 => "rgb(163, 124, 87)",
            5 => "rgb(145, 87, 168)",
            15 => "rgb(145, 87, 168)",
            31 => "rgb(145, 87, 168)",
            95 => "rgb(145, 87, 168)",
            6 => "rgb(207, 60, 60)",
            16 => "rgb(207, 60, 60)",
            21 => "rgb(207, 60, 60)",
            26 => "rgb(207, 60, 60)",
            96 => "rgb(207, 60, 60)",
            4 => "rgb(175, 175, 175)",
            7 => "rgb(175, 175, 175)",
            8 => "rgb(175, 175, 175)",
            17 => "rgb(175, 175, 175)",
            18 => "rgb(175, 175, 175)",
            33 => "rgb(175, 175, 175)",
            9 => "rgb(57, 196, 57)",
            19 => "rgb(57, 196, 57)",
            32 => "rgb(57, 196, 57)",
            34 => "rgb(57, 196, 57)",
            14 => "rgb(57, 196, 57)",
        ] as $id => $color) {
            Status::find($id)->update(["color" => $color]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn("color");
        });
    }
};
