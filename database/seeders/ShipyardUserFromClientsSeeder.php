<?php

namespace Database\Seeders;

use App\Models\Shipyard\User;
use App\Models\UserNote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShipyardUserFromClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserNote::all()->each(function ($note) {
            User::updateOrInsert([
                "id" => $note->user_id,
            ], [
                "id" => $note->user_id,
                "name" => $note->client_name,
                "email" => $note->email ?? "$note->user_id@test.test",
                "password" => Hash::make($note->password),
                "remember_token" => $note->remember_token,
                "created_at" => $note->created_at,
                "updated_at" => $note->updated_at,
            ]);
        });

        UserNote::whereIn("user_id", [
            0, 1,
        ])->delete();
    }
}
