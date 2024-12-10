<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class make extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url'
    ];

    public function models()
    {
        return $this->hasMany(carModel::class);
    }

    public function hireModels()
    {
        return $this->hasMany(CarHire::class);
    }
}
