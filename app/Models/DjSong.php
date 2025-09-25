<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DjSong extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

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
            "label" => "umiarkowane",
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
            "code" => 5,
        ],
    ];

    protected $fillable = [
        "id",
        "title", "artist",
        "key", "tempo", "genre_id", "changes_description",
        "songmap", "dj_sample_set_id",
        "lyrics", "chords", "samples", "extra_notes",
    ];

    public const PROCESSABLEJSONS = ["lyrics", "chords", "samples", "extra_notes"];

    protected $appends = [
        "full_title",
        "has_showcase_file",
        "tempo_pretty",
        "parts",
        "notes",
    ];

    #region attributes
    protected $casts = [
        "has_showcase_file" => "boolean",
        "lyrics" => "json",
        "chords" => "json",
        "samples" => "json",
        "extra_notes" => "json",
    ];

    public function getFullTitleAttribute()
    {
        return $this->artist ? "$this->artist – $this->title" : $this->title ?? "bez tytułu";
    }

    public function getTempoPrettyAttribute()
    {
        $tempo = collect(self::TEMPOS)->firstWhere("code", $this->tempo);
        if (!$tempo) return null;
        return "$tempo[icon] $tempo[label]";
    }

    public function getPartsAttribute()
    {
        if (empty($this->songmap)) return null;
        $parts = [];
        preg_match_all("/\w+/", $this->songmap, $parts);
        return $parts[0];
    }

    public function jsonForEdit($field)
    {
        if (empty($this->{$field})) return null;
        return collect($this->{$field})
            ->map(fn ($value, $part) => "//$part\n$value")
            ->join("\n\n");
    }

    public function getHasShowcaseFileAttribute(){
        return Storage::exists("showcases/$this->id.ogg");
    }

    //* compatibility with Songs
    public function getNotesAttribute() {
        return $this->changes_description;
    }
    #endregion

    #region relations
    public function sets()
    {
        return $this->belongsToMany(DjSet::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function sampleSet()
    {
        return $this->belongsTo(DjSampleSet::class, "dj_sample_set_id");
    }
    #endregion

    #region helpers
    public static function nextId()
    {
        $newest_id = DjSong::orderBy("id", "desc")->value("id") ?? "D000";
        $newest_id_last = substr($newest_id, 1);
        if(in_array($newest_id_last, ["ZZZ"])){
            return "D000";
        }
        return "D" . to_base36(from_base36($newest_id_last) + 1, 3);
    }

    public static function processJsonForEdit($value = null)
    {
        if (empty($value)) return null;
        return collect(preg_split("/(\r?\n){2}(?=\/\/)/", $value))
            ->mapWithKeys(function ($value) {
                $parts = [];
                preg_match("/^\/\/(\w+)\r?\n(.*)$/s", $value, $parts);
                return [$parts[1] => $parts[2]];
            });
    }
    #endregion
}
