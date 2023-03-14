<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayerDetailsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string("payer_name")->nullable();
            $table->string("payer_title")->nullable();
            $table->text("payer_address")->nullable();
            $table->text("payer_email")->nullable();
            $table->text("payer_phone")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn("payer_name");
            $table->dropColumn("payer_title");
            $table->dropColumn("payer_address");
            $table->dropColumn("payer_email");
            $table->dropColumn("payer_phone");
        });
    }
}
