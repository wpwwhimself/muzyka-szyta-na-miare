<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (["organ_showcases", "showcases", "dj_showcases"] as $table_name) {
            $class_name = implode("\\", [
                "App",
                "Models",
                Str::of($table_name)->singular()->studly(),
            ]);

            Schema::table($table_name, function (Blueprint $table) {
                $table->json("links")->nullable();
                $table->string("platform")->nullable()->change();
            });

            $class_name::all()->each(fn ($s) => $s->update([
                "links" => [$s->platform => $s->link],
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (["organ_showcases", "showcases", "dj_showcases"] as $table_name) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->dropColumn("links");
                $table->string("platform")->change();
            });
        }
    }
};
