<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\car_hire\CarHire;
use App\Models\CarHire as ModelsCarHire;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName', 'lastName', 'email', 'password', 'phoneNumber', 'verify'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     // Relationship with Reviews model
     public function reviews()
     {
         return $this->hasMany(Reviews::class, 'user_id');
     }

      // Relationship with CarHire model
     public function carHires()
     {
        return $this->hasMany(ModelsCarHire::class, 'user_id');
     }

     // Relationship with Payment model
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');  // This establishes the relationship
    }
}
