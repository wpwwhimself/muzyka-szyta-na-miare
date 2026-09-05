<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (env("APP_ENV") !== "local"
            || User::find(1)->display_name === "archmage"
        ) return;

        User::find(1)->update([
            "name" => "arch",
            "display_name" => "archmage",
            "email" => "kontakt@muzykaszytanamiare.pl",
            "password" => Hash::make("archmage"),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
