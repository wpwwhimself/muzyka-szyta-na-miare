<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("dj_sample_sets", function (Blueprint $table) {
            $table->string("id")->primary();
            $table->string("name");
            $table->text("description")->nullable();
            $table->timestamps();
        });

        Schema::table('dj_songs', function (Blueprint $table) {
            $table->json("samples")->nullable()->after("chords");
            $table->dropColumn("has_project_file");
            $table->string("dj_sample_set_id")->nullable()->after("songmap");
                $table->foreign("dj_sample_set_id")->references("id")->on("dj_sample_sets")->nullOnDelete()->cascadeOnUpdate();
            $table->renameColumn("notes", "extra_notes");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dj_songs', function (Blueprint $table) {
            $table->dropColumn("samples");
            $table->dropConstrainedForeignId("dj_sample_set_id");
            $table->boolean("has_project_file")->default(false)->after("songmap");
            $table->renameColumn("extra_notes", "notes");
        });

        Schema::dropIfExists("dj_sample_sets");
    }
};
