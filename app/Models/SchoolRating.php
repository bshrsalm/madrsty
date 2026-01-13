<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'education_level',
        'support',
        'teachers',
        'follow_up',
        'trips',
         'parent_communication',
    'exams',
    'enrichment_curriculum',
    'school_management',
    'school_environment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
