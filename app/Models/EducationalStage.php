<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalStage extends Model
{
    protected $fillable = ['name'];

    public function types()
    {
        return $this->hasMany(StageType::class);
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}