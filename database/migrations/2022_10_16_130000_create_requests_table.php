<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("surname")->nullable();
            $table->string("email")->nullable();
            $table->integer("phone")->nullable();
            $table->string("other_medium")->nullable();
            $table->foreignId("user_id")->nullable()->constrained();
            $table->string("contact_preference")->default("email");
            $table->boolean("made_by_me")->default(false);
            $table->string("quest_type");
            $table->string("link")->nullable();
            $table->text("wishes")->nullable();
            $table->date("deadline")->nullable();
            $table->foreignId("status_id")->constrained("statuses");
            $table->foreignId("quest_id")->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
