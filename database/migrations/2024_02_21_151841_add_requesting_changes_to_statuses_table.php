<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRequestingChangesToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("statuses")
            ->insert([
                ["id" => 21, "status_name" => "zaproponowano zmiany", "status_symbol" => "fa-hand-point-up"]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("statuses")->where("id", 21)->delete();
    }
}
