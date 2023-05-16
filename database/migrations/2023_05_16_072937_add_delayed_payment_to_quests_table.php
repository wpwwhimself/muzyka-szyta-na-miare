<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDelayedPaymentToQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->date("delayed_payment")->nullable();
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->date("delayed_payment")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn("delayed_payment");
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn("delayed_payment");
        });
    }
}
