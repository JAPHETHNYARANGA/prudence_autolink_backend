<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
    ];

    // Relationship to get messages in this conversation
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relationship to get the first user
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    // Relationship to get the second user
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }
}
