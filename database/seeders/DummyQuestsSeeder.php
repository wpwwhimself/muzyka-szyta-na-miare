<?php

namespace Database\Seeders;

use App\Models\Quest;
use App\Models\Request;
use App\Models\Song;
use Illuminate\Database\Seeder;

class DummyQuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Song::insert([
            [
                "id" => "P000",
                "title" => "Hello world",
                "artist" => "Jon Bovi",
                "link" => "http://wpww.pl",
                "price_code" => "c",
            ],
            [
                "id" => "P001",
                "title" => "Double Trouble",
                "artist" => "Evanescence",
                "link" => "http://wpww.pl",
                "price_code" => "bxa"
            ]
        ]);
        Quest::insert([
            [
                "id" => "P22-00",
                "song_id" => "P002",
                "client_id" => 2,
                "status_id" => 11,
                "price_code_override" => "c",
                "price" => 70,
                "paid" => 0,
                "deadline" => "2022-12-31",
                "created_at" => "2022-11-18 15:15:18",
                "updated_at" => "2022-11-18 15:15:18"
            ]
        ]);
    }
}
