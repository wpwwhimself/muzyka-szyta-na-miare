<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->char("indicator")->unique();
            $table->string("service");
            $table->bigInteger("quest_type_id")->nullable();
            // $table->foreignId("quest_type_id")->nullable()->constrained();
            $table->char("operation")->comment("+: addition, *: multiplication");
            $table->float("price_a")->nullable();
            $table->float("price_b")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
