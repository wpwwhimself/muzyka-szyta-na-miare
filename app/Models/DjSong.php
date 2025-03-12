<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DjSong extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    public const TEMPOS = [
        [
            "label" => "ślimacze",
            "icon" => "🐌",
            "code" => 1,
        ],
        [
            "label" => "wolne",
            "icon" => "🐢",
            "code" => 2,
        ],
        [
            "label" => "średnie",
            "icon" => "🙂",
            "code" => 3,
        ],
        [
            "label" => "szybkie",
            "icon" => "🐶",
            "code" => 4,
        ],
        [
            "label" => "padaka",
            "icon" => "🐗",
            "code" => 4,
        ],
    ];

    protected $fillable = [
        "id",
        "title", "artist",
        "key", "tempo",
        "songmap",
        "lyrics", "chords",
    ];

    protected $appends = [
        "full_title",
    ];

    #region attributes
    public function fullTitle()
    {
        return Attribute::make(
            get: fn () => $this->artist ? "$this->artist – $this->title" : $this->title ?? "bez tytułu",
        );
    }
    #endregion
}
