<?php

use App\Models\Genre;
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
            ["name" => "szybka przeróbka"],
            ["name" => "songwriter"],
            ["name" => "gitary"],
            ["name" => "gitary + elektro"],
            ["name" => "gitary + orkiestra"],
            ["name" => "elektro"],
            ["name" => "orkiestra"],
            ["name" => "jazz"],
            ["name" => "folk"],
        ] as $g) {
            if (Genre::where("name", $g["name"])->exists()) continue;
            Genre::insert($g);
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
