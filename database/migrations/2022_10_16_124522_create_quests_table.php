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
            $table->string("id", 4)->primary();
            $table->foreignId("quest_type_id")->constrained();
            $table->foreignId("song_id")->constrained();
            $table->foreignId("client_id")->constrained();
            $table->foreignId("status_id")->constrained("statuses");
            $table->string("link")->nullable();
            $table->text("wishes")->nullable();
            $table->string("price_code");
            $table->float("price");
            $table->float("paid")->default(0);
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
