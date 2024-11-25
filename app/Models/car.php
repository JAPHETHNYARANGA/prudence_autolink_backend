<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'make_id',
        'model_id',
        'sold',
        'year',
        'transmission',
        'price',
        'description',
        'category_id',
        'views',
        'location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function make()
    {
        return $this->belongsTo(make::class);
    }

    public function model()
    {
        return $this->belongsTo(carModel::class);
    }

    public function images()
    {
        return $this->hasMany(image::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'car_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($car) {
            $car->favorites()->delete();
        });
    }
}
