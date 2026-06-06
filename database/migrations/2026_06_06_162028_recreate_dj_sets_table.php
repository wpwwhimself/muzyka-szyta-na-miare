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
        Schema::create("dj_sets", function (Blueprint $table) {
            $table->string("id", 4)->primary();
            $table->string("name");
            $table->foreignId("genre_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string("compositions")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("dj_sets");
    }
};
