<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->foreignId("id")->primary()->constrained("users");
            $table->string("client_name");
            $table->string("email")->nullable();
            $table->integer("phone")->nullable();
            $table->string("other_medium")->nullable();
            $table->string("contact_preference")->default("email");
            $table->integer("trust")->default(0)->comment("1: bigger trust, -1: swindler");
            $table->float("budget")->default(0);
            $table->text("default_wishes")->nullable();
            $table->text("special_prices")->nullable();
            $table->integer("helped_showcasing")->default(0)->comment("0: no, 1: pending, 2: yes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
