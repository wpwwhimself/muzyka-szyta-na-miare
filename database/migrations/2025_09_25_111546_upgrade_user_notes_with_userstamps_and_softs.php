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
        Schema::rename("clients", "user_notes");
        Schema::table("user_notes", function (Blueprint $table) {
            $table->renameColumn("id", "user_id");
            $table->softDeletes();
            // $table->timestamps();
            $table->foreignId("created_by")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("deleted_by")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_notes", function (Blueprint $table) {
            $table->dropConstrainedForeignId("created_by");
            $table->dropConstrainedForeignId("updated_by");
            $table->dropConstrainedForeignId("deleted_by");
            $table->dropSoftDeletes();
            $table->renameColumn("user_id", "id");
        });
        Schema::rename("user_notes", "clients");
    }
};
