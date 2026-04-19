<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SchoolRatinginspector extends Model
{
    protected $table = 'school_ratings_inspectors';
    protected $fillable = ['school_id', 'inspector_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }

public function scores()
{
    return $this->hasMany(SchoolRatingScore::class, 'school_rating_id');
}
    public function getAverageAttribute()
    {
        if ($this->scores->isEmpty()) return 0;
        return round($this->scores->avg('score'), 1);
    }
}