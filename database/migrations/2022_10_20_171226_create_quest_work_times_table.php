<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestWorkTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_work_times', function (Blueprint $table) {
            $table->id();
            $table->string("song_id");
                $table->foreign("song_id")->references("id")->on("songs");
            $table->foreignId("status_id")->constrained("statuses");
            $table->time("time_spent")->default(0);
            $table->boolean("now_working")->default(false);
            $table->timestamp("since");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quest_work_times');
    }
}
