<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageType extends Model
{
    protected $fillable = [
        'name', 
    'educational_stage_id'
    ];

    public function stage()
    {
        return $this->belongsTo(EducationalStage::class, 'educational_stage_id');
    }
}
