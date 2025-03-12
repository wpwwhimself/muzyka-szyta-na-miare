<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDjSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dj_songs', function (Blueprint $table) {
            $table->string("id")->primary();
            $table->string("title");
            $table->string("artist")->nullable();
            $table->string("key")->nullable();
            $table->integer("tempo")->nullable();
            $table->string("songmap")->nullable();
            $table->json("lyrics")->nullable();
            $table->json("chords")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dj_songs');
    }
}
