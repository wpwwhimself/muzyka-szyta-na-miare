<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArrangementDetailsColumnsToDjSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dj_songs', function (Blueprint $table) {
            $table->foreignId("genre_id")->nullable()->constrained();
            $table->text('changes_description')->nullable();
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
            $table->dropColumn(["genre_id", "changes_description"]);
        });
    }
}
