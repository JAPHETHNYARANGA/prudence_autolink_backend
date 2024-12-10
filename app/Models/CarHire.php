<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarHire extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'make_id',
        'model_id',
        'category_id',
        'year',
        'transmission',
        'price',
        'description',
        'category_id',
        'price',
        'frequency',//frequency means whether cost is daily or hourly, 0 is daily 1 is hourly
        'location',
        'available'
    ];

     // Relationship with User model
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id');
     }

     public function carHireImages()
     {
         return $this->hasMany(CarHireImages::class, 'car_id');
     }

     public function make()
     {
         return $this->belongsTo(make::class);
     }
 
     public function model()
     {
         return $this->belongsTo(carModel::class);
     }
     
}
