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
        Schema::table('dj_showcases', function (Blueprint $table) {
            $table->foreignId("composition_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dj_showcases', function (Blueprint $table) {
            $table->dropForeign("dj_showcases_composition_id_foreign");
            $table->dropColumn("composition_id");
        });
    }
};
