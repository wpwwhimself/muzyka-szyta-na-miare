<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        "song_id",
        "variant_name", "version_name",
        "transposition",
        "only_for_client_id",
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
    public function exclusiveClient()
    {
        return $this->belongsTo(User::class, "only_for_client_id");
    }
}
