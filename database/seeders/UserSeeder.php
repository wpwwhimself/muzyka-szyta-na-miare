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
            ["password" => "kalafior"],
            ["password" => "marchewka"],
            ["password" => "marchewkb"],
            ["password" => "marchewkc"],
            ["password" => "marchewkd"],
            ["password" => "marchewke"],
            ["password" => "marchewkf"],
        ]);
        DB::table("clients")->insert([
            [
                "id" => 2,
                "client_name" => "Jan Testowy",
                "email" => "bob@gmail.com",
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
            [
                "id" => 3,
                "client_name" => "Monika Testowy",
                "email" => "cob@gmail.com",
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
            [
                "id" => 4,
                "client_name" => "Andrzej Testowy",
                "email" => "dob@gmail.com",
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
            [
                "id" => 5,
                "client_name" => "Szczepan Testowy",
                "email" => null,
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
            [
                "id" => 6,
                "client_name" => "Jadwiga Testowy",
                "email" => "fob@gmail.com",
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
            [
                "id" => 7,
                "client_name" => "Agnes Testowy",
                "email" => "uob@gmail.com",
                "phone" => 123456789,
                "created_at" => "2022-01-01",
                "updated_at" => "2022-01-01"
            ],
        ]);
    }
}
