<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ["name", "desc"];

    public function costs(){
        return $this->hasMany(Cost::class);
    }
}
