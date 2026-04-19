<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolType extends Model
{
    protected $fillable = ['name'];

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}