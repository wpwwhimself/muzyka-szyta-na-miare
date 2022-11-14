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
    }
}
