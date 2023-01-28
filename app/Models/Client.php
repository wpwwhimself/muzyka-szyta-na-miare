<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['helped_showcasing', 'budget', 'trust'];

    public function user(){
        return $this->belongsTo(User::class, "id", "id");
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    public function isMailable(){
        return ($this->email && $this->contact_preference == "email");
    }
}
