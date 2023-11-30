<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFilesReadyToQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->boolean("files_ready")->default(false);
        });
        DB::table("statuses")->where("id", 19)->update(["status_symbol" => "fa-check-double"]);
        DB::table("statuses")->insert(["id" => 14, "status_name" => "póki co zaakceptowane", "status_symbol" => "fa-check"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn("files_ready");
        });
        DB::table("statuses")->where("id", 19)->update(["status_symbol" => "fa-check"]);
        DB::table("statuses")->where("id", 14)->delete();
    }
}
