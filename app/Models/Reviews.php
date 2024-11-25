<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'review',
        'reviewer_id'
    ];

      // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
