<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransactionBackup extends Model
{
    protected $table = 'payment_transaction_backups';

    public $timestamps = false;

    protected $fillable = [
        'payment_transaction_id',
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
        'backed_up_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'transacted_at' => 'datetime',
        'expired_at' => 'datetime',
        'backed_up_at' => 'datetime',
    ];

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class);
    }

    public function actedByUser()
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }
}
