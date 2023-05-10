<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrimaryToInvoiceQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->boolean("primary")->default(true);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn("primary");
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
            $table->dropColumn("primary");
        });
        Schema::table('invoice_quests', function (Blueprint $table) {
            $table->boolean("primary")->after("id");
        });
    }
}
