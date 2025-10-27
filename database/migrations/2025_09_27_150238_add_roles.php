<?php

use App\Models\Shipyard\Role;
use App\Models\User;
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
        Role::insert([
            [
                "name" => "client",
                "icon" => "account-tie",
                "description" => "Ma dostęp do swoich zapytań i zleceń.",
            ],
        ]);

        User::all()->each(function ($user) {
            if ($user->email == "kontakt@muzykaszytanamiare.pl") return;
            $user->roles()->attach("client");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::whereIn("name", [
            "client",
        ])->delete();
    }
};
