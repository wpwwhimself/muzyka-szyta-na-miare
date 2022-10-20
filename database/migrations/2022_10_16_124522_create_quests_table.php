<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("song_id")->constrained();
            $table->foreignId("client_id")->constrained();
            $table->foreignId("status_id")->constrained();
            $table->string("link")->nullable();
            $table->text("wishes")->nullable();
            $table->text("price");
            $table->float("paid")->default(0);
            $table->date("deadline")->nullable();
            $table->boolean("hard_deadline")->default(false);
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
        Schema::dropIfExists('quests');
    }
}
