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
            $table->uuid("id")->primary();
            $table->boolean("made_by_me")->default(false);

            $table->foreignId("client_id")->nullable()->constrained();
            $table->string("client_name")->nullable();
            $table->string("email")->nullable();
            $table->integer("phone")->nullable();
            $table->string("other_medium")->nullable();
            $table->string("contact_preference")->nullable()->default("email");

            $table->foreignId("song_id")->nullable()->constrained();
            $table->foreignId("quest_type_id")->nullable()->constrained();
            $table->string("title")->nullable();
            $table->string("artist")->nullable();
            $table->string("cover_artist")->nullable();
            $table->string("link")->nullable();
            $table->text("wishes")->nullable();
            $table->string("price_code")->nullable();

            $table->float("price")->nullable();
            $table->date("deadline")->nullable();
            $table->date("hard_deadline")->nullable();
            $table->foreignId("status_id")->constrained("statuses");
            $table->string("quest_id")->nullable();
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
