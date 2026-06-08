<?php

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
        Schema::table('dj_sets', function (Blueprint $table) {
            $table->dropColumn("compositions");
        });

        Schema::table("compositions", function (Blueprint $table) {
            $table->string("dj_set_id")->nullable();
                $table->foreign("dj_set_id")->references("id")->on("dj_sets")->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dj_sets', function (Blueprint $table) {
            $table->string("compositions")->nullable();
        });

        Schema::table("compositions", function (Blueprint $table) {
            $table->dropColumn("dj_set_id");
        });
    }
};
