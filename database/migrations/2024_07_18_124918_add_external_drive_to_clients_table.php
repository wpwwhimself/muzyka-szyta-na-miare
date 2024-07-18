<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalDriveToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string("external_drive")->nullable();
        });
        Schema::table('quests', function (Blueprint $table) {
            $table->boolean("has_files_on_external_drive")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn("external_drive");
        });
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn("has_files_on_external_drive");
        });
    }
}
