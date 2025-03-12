<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraBaseColumnsToDjSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dj_songs', function (Blueprint $table) {
            $table->json("notes")->nullable()->after("chords");
            $table->boolean("has_project_file")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dj_songs', function (Blueprint $table) {
            $table->dropColumn(["notes", "has_project_file"]);
        });
    }
}
