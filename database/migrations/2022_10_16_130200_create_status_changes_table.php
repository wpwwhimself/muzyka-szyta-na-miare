<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_changes', function (Blueprint $table) {
            $table->id();
            $table->string("quest_id", 4)->nullable();
                $table->foreign("quest_id")->references("id")->on("quests");
            $table->foreignId("status_id")->constrained("statuses");
            $table->foreignId("changed_by")->references("id")->on("users");
            $table->text("comment")->nullable();
            $table->dateTime("date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_changes');
    }
}
