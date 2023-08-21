<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadingToSongIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('song_work_times', function (Blueprint $table) {
            $table->dropForeign("song_work_times_song_id_foreign");
            $table->foreign("song_id")->references("id")->on("songs")->onUpdate("cascade");
        });
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->dropForeign("invoice_quests_quest_id_foreign");
            $table->foreign("quest_id")->references("id")->on("quests")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('song_work_times', function (Blueprint $table) {
            $table->dropForeign("song_work_times_song_id_foreign");
            $table->foreign("song_id")->references("id")->on("songs");
        });
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->dropForeign("invoice_quests_quest_id_foreign");
            $table->foreign("quest_id")->references("id")->on("quests");
        });
    }
}
