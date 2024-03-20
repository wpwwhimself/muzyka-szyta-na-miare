<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddReturnPaymentStatusToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("statuses")->insert(["id" => 34, "status_name" => "dokonano zwrotu wpłaty", "status_symbol" => "fa-hand-holding-dollar"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("statuses")->where("id", 34)->delete();
    }
}
