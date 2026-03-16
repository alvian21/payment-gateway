<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'payment_order_id',
        'reff',
        'status',
        'source',
        'acted_by_user_id',
        'transacted_at',
        'amount',
        'customer_name',
        'code',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'transacted_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class);
    }

    public function actedByUser()
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }

    public function backup()
    {
        return $this->hasOne(PaymentTransactionBackup::class);
    }
}
