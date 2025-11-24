<?php

use App\Models\Composition;
use App\Models\Song;
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
        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();

            $table->string("composer")->nullable();

            $table->timestamps();
        });

        Schema::create("composition_song_tag", function (Blueprint $table) {
            $table->id();
            $table->foreignId("composition_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("song_tag_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table("songs", function (Blueprint $table) {
            $table->foreignId("composition_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table("requests", function (Blueprint $table) {
            $table->foreignId("composition_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
        });

        Song::all()->each(function (Song $s) {
            $tune = Composition::create([
                "title" => $s->title,
                "composer" => $s->artist,
            ]);
            $tune->tags()->sync($s->tags->pluck("id")->toArray());

            $s->update([
                "composition_id" => $tune->id,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("songs", function (Blueprint $table) {
            $table->dropConstrainedForeignId("composition_id");
        });

        Schema::dropIfExists("composition_song_tag");
        Schema::dropIfExists('compositions');
    }
};
