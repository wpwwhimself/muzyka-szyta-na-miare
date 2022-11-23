<?php

namespace Database\Seeders;

use App\Models\Payment;
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
                "genre_id" => 1
            ],
            [
                "id" => "P001",
                "title" => "Double Trouble",
                "artist" => "Evanescence",
                "link" => "http://wpww.pl",
                "price_code" => "bxa",
                "genre_id" => 2
            ]
        ]);
        Quest::insert([
            [
                "id" => "P22-00",
                "song_id" => "P001",
                "client_id" => 2,
                "status_id" => 11,
                "price_code_override" => "c",
                "price" => 50,
                "deadline" => "2022-12-31",
                "created_at" => "2022-11-18 15:15:18",
                "updated_at" => "2022-11-18 15:15:18"
            ],
            [
                "id" => "P22-01",
                "song_id" => "P000",
                "client_id" => 2,
                "status_id" => 12,
                "price_code_override" => "cc",
                "price" => 100,
                "deadline" => "2022-12-30",
                "created_at" => "2022-11-19 15:15:18",
                "updated_at" => "2022-11-19 15:15:18"
            ]
        ]);
    }
}
