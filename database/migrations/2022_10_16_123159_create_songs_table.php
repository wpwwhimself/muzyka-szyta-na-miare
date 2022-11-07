<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("artist");
            $table->string("cover_artist")->nullable();
            $table->string("link")->nullable();
            $table->foreignId("quest_type_id")->constrained();
            $table->string("genre");
            $table->integer("instruments_code")->default(0)->comment("drums, guitars, pianos, synths, brass, strings, vocals, others");
            $table->string("price_code");
            $table->text("notes")->nullable();
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
        Schema::dropIfExists('songs');
    }
}
