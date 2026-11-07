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
        Schema::table('file_tags', function (Blueprint $table) {
            $table->boolean("is_local")->default(false);
        });
        Schema::table("files", function (Blueprint $table) {
            $table->dropColumn("needs_replacing");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_tags', function (Blueprint $table) {
            $table->dropColumn("is_local");
        });
        Schema::table("files", function (Blueprint $table) {
            $table->boolean("needs_replacing")->default(false);
        });
    }
};
