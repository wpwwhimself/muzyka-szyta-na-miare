<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        "status_id"
    ];

    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
}
