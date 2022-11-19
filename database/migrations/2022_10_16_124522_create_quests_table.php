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
            $table->string("id")->primary();
            $table->string("song_id");
                $table->foreign("song_id")->references("id")->on("songs");
            $table->foreignId("client_id")->constrained();
            $table->foreignId("status_id")->constrained("statuses");
            $table->string("price_code_override")->nullable();
            $table->float("price");
            $table->date("deadline")->nullable();
            $table->date("hard_deadline")->nullable();
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
