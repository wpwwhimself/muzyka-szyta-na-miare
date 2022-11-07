<?php

namespace Database\Seeders;

use App\Models\Song;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
    }
}
