<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SchoolRatingScore extends Model
{
     protected $table = 'school_rating_scores';
    protected $fillable = [
        'school_rating_id', 
        'rating_criteria_id',
         'score'
         ];

  public function criteria()
{
    return $this->belongsTo(RatingCriteria::class, 'rating_criteria_id');
}
}