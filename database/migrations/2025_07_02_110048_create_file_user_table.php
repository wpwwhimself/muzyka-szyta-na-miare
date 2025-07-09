<?php

use App\Models\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("file_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });

        File::all()->each(function ($file) {
            $file->exclusiveClients()->attach($file->only_for_client_id);
        });

        Schema::table("files", function (Blueprint $table) {
            $table->dropConstrainedForeignId("only_for_client_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("files", function (Blueprint $table) {
            $table->foreignId("only_for_client_id")->nullable()->constrained("users", "id")->cascadeOnDelete()->cascadeOnUpdate();
        });

        File::all()->each(function ($file) {
            $file->update(["only_for_client_id" => $file->exclusiveClients()->first()->user_id]);
        });

        Schema::dropIfExists('file_user');
    }
}
