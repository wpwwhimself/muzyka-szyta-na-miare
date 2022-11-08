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
            $table->string("re_quest_id")->nullable();
            $table->foreignId("new_status_id")->constrained("statuses");
            $table->foreignId("changed_by")->nullable()->references("id")->on("users");
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
