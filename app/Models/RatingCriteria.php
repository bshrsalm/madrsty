<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RatingCriteria extends Model
{
    protected $table = 'rating_criteria';
    protected $fillable = [
        'name'
        ];
}