<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountsToInvoiceQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->float("amount");
            $table->float("paid")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->dropColumn("amount");
            $table->dropColumn("paid");
        });
    }
}
