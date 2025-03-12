<?php

namespace App\Http\Controllers;

use App\Models\DjSong;
use Illuminate\Http\Request;

class DjController extends Controller
{
    #region songs
    public function listSongs()
    {
        $songs = DjSong::orderBy("name")->paginate(25);

        return view("dj.songs.list", compact(
            "songs",
        ));
    }
    #endregion
}
