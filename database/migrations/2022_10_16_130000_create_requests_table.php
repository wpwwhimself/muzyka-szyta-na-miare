<?php

use App\Models\Client;
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
            $table->string("client_name");
            $table->string("email")->nullable();
            $table->integer("phone")->nullable();
            $table->string("other_medium")->nullable();
            $table->foreignId("client_id")->nullable()->constrained();
            $table->string("contact_preference")->default("email");
            $table->boolean("made_by_me")->default(false);
            $table->string("quest_type");
            $table->string("link")->nullable();
            $table->text("wishes")->nullable();
            $table->date("deadline")->nullable();
            $table->boolean("hard_deadline")->default(false);
            $table->foreignId("status_id")->constrained("statuses");
            $table->string("title")->nullable();
            $table->string("artist")->nullable();
            $table->string("cover_artist")->nullable();
            $table->string("price")->nullable();
            $table->string("quest_id", 4)->nullable();
                $table->foreign("quest_id")->references("id")->on("quests");
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
        Schema::dropIfExists('requests');
    }
}
