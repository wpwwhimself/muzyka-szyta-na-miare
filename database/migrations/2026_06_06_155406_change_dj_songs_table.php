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
        Schema::drop("dj_set_dj_song");
        Schema::drop("dj_sets");
        Schema::drop("dj_songs");
        Schema::drop("dj_sample_sets");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
