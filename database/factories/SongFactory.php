<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "id" => next_song_id(1),
            "title" => $this->faker->catchPhrase(),
            "artist" => $this->faker->name(),
            "link" => $this->faker->url(),
            "price_code" => "c",
            "notes" => $this->faker->sentence(),
        ];
    }
}
