<?php

use App\Http\Controllers\Shipyard\AuthController;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::all()->each(fn ($u) => $u->update([
            "name" => substr($u->notes?->password ?? Str::random(4), 0, AuthController::NOLOGIN_LOGIN_PART_LENGTH),
        ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::all()->each(fn ($u) => $u->update([
            "name" => $u->notes?->client_name ?? Str::random(8),
        ]));
    }
};
