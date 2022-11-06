<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            [
                "id" => 1,
                "password" => "kalafior"
            ],
            [
                "id" => 2,
                "password" => "marchewka"
            ]
        ]);
        DB::table("clients")->insert([
            "id" => 2,
            "client_name" => "Jan Testowy",
            "email" => "bob@gmail.com",
            "phone" => 123456789,
            "created_at" => "2022-01-01",
            "updated_at" => "2022-01-01"
        ]);
    }
}
