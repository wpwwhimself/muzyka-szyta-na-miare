<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDjSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dj_sets', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description");
            $table->timestamps();
        });

        Schema::create("dj_set_dj_song", function (Blueprint $table) {
            $table->id();
            $table->string("dj_song_id");
                $table->foreign("dj_song_id")->references("id")->on("dj_songs")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("dj_set_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dj_set_dj_song');
        Schema::dropIfExists('dj_sets');
    }
}
