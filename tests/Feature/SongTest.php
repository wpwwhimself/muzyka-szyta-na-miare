<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongTest extends TestCase
{
    use DatabaseTransactions;

    public function testArchmageCanEditSong()
    {
        $admin = User::find(1);
        $song = Song::factory()->create();

        $res = $this->actingAs($admin)->get(route("song-edit", [
            "id" => $song->id,
        ]));
        $res->assertOk();
        $res->assertViewIs("archmage.song.edit");
    }
}
