<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    protected $table = 'payment_orders';

    protected $fillable = [
        'reff',
        'customer_name',
        'hp',
        'code',
        'base_amount',
        'fee',
        'amount',
        'expired_at',
        'paid_at',
        'status',
        'flagged_by_user_id',
        'flagged_at',
    ];

    protected $casts = [
        'base_amount' => 'integer',
        'fee' => 'integer',
        'amount' => 'integer',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'flagged_at' => 'datetime',
    ];

    public function flaggedByUser()
    {
        return $this->belongsTo(User::class, 'flagged_by_user_id');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
