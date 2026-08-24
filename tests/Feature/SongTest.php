<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SongTest extends TestCase
{
    use DatabaseTransactions;

    public function testArchmageCanEditSong()
    {
        $admin = User::find(1);
        $song = Song::factory()->create();
        $newTitle = Str::random(10);

        $res = $this->actingAs($admin)
            ->from(route("song-edit", [
                "id" => $song->id,
            ]))
            ->get(route("song-process", [
                "id" => $song->id,
                "title" => $newTitle,
            ]));
        $res->assertOk();
        $res->assertSessionHas("success");
        $res->assertViewIs("archmage.songs.list");
        $res->assert($song->fresh()->title == $newTitle);
    }
}
