<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        "song_id",
        "variant_name", "version_name",
        "transposition",
        "description",
        "file_paths",
    ];
    protected $casts = ["file_paths" => "array"];

    public function tags()
    {
        return $this->belongsToMany(FileTag::class);
    }
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
    public function exclusiveClients()
    {
        return $this->belongsToMany(User::class, "file_user", "file_id", "user_id");
    }

    public function getMissingFilesAttribute()
    {
        $missing = [];

        foreach ($this->file_paths as $extension => $path) {
            if (!Storage::exists($path)) {
                $missing[$extension] = $path;
            }
        }

        return $missing;
    }
}
