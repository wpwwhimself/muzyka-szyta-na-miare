<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("file_tags", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("icon");
            $table->string("color");
            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string("song_id");
                $table->foreign("song_id")->references("id")->on("songs")->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("variant_name");
            $table->string("version_name");
            $table->integer("transposition")->default(0);
            $table->foreignId("only_for_client_id")->nullable()->constrained("users", "id")->cascadeOnDelete()->cascadeOnUpdate();
            $table->text("description")->nullable();
            $table->json("file_paths");
            $table->timestamps();
        });

        Schema::create("file_tag", function (Blueprint $table) {
            $table->id();
            $table->foreignId("file_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("file_tag_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('file_tag');
        Schema::dropIfExists('files');
        Schema::dropIfExists('file_tags');
    }
}
