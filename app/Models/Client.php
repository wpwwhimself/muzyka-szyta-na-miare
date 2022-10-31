<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class, "id", "id");
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    public function isVeteran(){
        $veteran_from = DB::table("settings")->where("setting_name", "veteran_from")->get(["value_str"]);

    }
    public function pricing(){
        //
    }
}
