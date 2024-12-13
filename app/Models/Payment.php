<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'payment_date',
        'next_payment_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Checks if the user has an active subscription.
     */
    public function isActiveSubscription()
    {
        return $this->status === 'active' && $this->next_payment_date > now();
    }
}
