<?php

namespace Database\Seeders;

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
                "quest_type_id" => 1,
                "title" => "Hello world",
                "artist" => "Jon Bovi",
                "cover_artist" => null,
                "link" => "http://wpww.pl",
                "genre" => "blues",
                "instruments_code" => "15",
                "price_code" => "cx"
            ],
            [
                "quest_type_id" => 2,
                "title" => "Double Trouble",
                "artist" => "Evanescence",
                "cover_artist" => "Bweee",
                "link" => "http://wpww.pl",
                "genre" => "jazz",
                "instruments_code" => "13",
                "price_code" => "b"
            ]
        ]);
        // Request::insert([
        //     "made_by_me" => true,
        //     "client_id" => 2,
        //     "quest_type_id" => 1, "title" => "Aaaa", "artist" => "Bbbb Bbb",
        //     "status_id" => 1
        // ]);
        // Request::insert([
        //     "made_by_me" => false,
        //     "client_name" => "Agata Kowalska", "email" => "lowcy.b@aaa.com",
        //     "song_id" => 2,
        //     "price_code" => "c", "price" => 70, "deadline" => "2022-12-13",
        //     "status_id" => 5
        // ]);
    }
}
