<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table("migrations")->updateOrInsert([
            "migration" => "2025_02_21_211331_shipyard_create_personal_access_tokens_table",
        ], [
            "batch" => 1,
        ]);
        DB::table("migrations")->updateOrInsert([
            "migration" => "2025_09_24_142158_shipyard_create_local_settings_table",
        ], [
            "batch" => 1,
        ]);

        Schema::rename("users", "clients");
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password')->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users");
        Schema::rename("clients", "users");
        DB::table("migrations")->where("migration", "=", "2025_02_21_211331_shipyard_create_personal_access_tokens_table")->delete();
    }
};
