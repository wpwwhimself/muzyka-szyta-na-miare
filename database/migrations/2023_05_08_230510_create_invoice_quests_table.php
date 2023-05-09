<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("invoice_id")->constrained();
            $table->string("quest_id");
                $table->foreign("quest_id")->references("id")->on("quests");
            $table->timestamps();
        });

        Schema::table("invoices", function (Blueprint $table) {
            $table->dropConstrainedForeignId("quest_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_quests');
        Schema::table("invoices", function (Blueprint $table){
            $table->string("quest_id")->nullable()->after("id");
                $table->foreign("quest_id")->references("id")->on("quests");
        });
    }
}
