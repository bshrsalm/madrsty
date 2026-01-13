<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'student_gender',
        'address',
        'phone',
        'registration_fee',
        'tuition',
        'description',
        'facebook',
        'instagram',
        'link_web',
        'google_map',
        'manager_id',
        'barcode_token',
        'barcode_image',

        
    ];

    public function manager()
    {
        return $this->hasOne(User::class, 'school_id')->where('role', 'Manager');
    
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
     public function ratings()
    {
      return $this->belongsTo(User::class, 'manager_id');
    }

}