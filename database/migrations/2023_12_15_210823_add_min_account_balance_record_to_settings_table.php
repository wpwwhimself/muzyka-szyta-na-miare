<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMinAccountBalanceRecordToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("settings")->insert([
            "setting_name" => "min_account_balance",
            "desc" => "Minimalne saldo konta (na potrzeby obliczania wypłaty)",
            "value_str" => 750
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("settings")->where("setting_name", "min_account_balance")->delete();
    }
}
