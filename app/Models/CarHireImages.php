<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarHireImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'url'
    ];


    public function carHire()
    {
        return $this->belongsTo(CarHire::class, 'car_id'); // Specify the foreign key explicitly
    }
}
