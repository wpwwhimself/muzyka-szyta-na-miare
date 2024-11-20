<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongSongTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_song_tag', function (Blueprint $table) {
            $table->id();
            $table->string("song_id");
                $table->foreign("song_id")->references("id")->on("songs")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId("song_tag_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('song_song_tag');
    }
}
