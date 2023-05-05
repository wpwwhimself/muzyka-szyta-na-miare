<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuestType extends Model
{
    use HasFactory;

    public function quests(){
        return Quest::where("id", "like", $this->code."%");
    }
}
