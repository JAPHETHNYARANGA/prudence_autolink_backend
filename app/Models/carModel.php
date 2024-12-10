<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'make_id',
        'name'
    ];

    public function make()
    {
        return $this->belongsTo(make::class);
    }

    public function cars()
    {
        return $this->hasMany(car::class);
    }

    public function hireCars()
    {
        return $this->hasMany(CarHire::class);
    }
}
