<?php

namespace App\Listeners;

use App\Events\PaymentTransactionCreated;
use App\Models\PaymentTransactionBackup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BackupPaymentTransaction implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentTransactionCreated $event): void
    {
        $tx = $event->transaction;

        PaymentTransactionBackup::create([
            'payment_transaction_id' => $tx->id,
            'payment_order_id' => $tx->payment_order_id,
            'reff' => $tx->reff,
            'status' => $tx->status,
            'source' => $tx->source,
            'acted_by_user_id' => $tx->acted_by_user_id,
            'transacted_at' => $tx->transacted_at ?? now(),
            'amount' => $tx->amount,
            'customer_name' => $tx->customer_name,
            'code' => $tx->code,
            'expired_at' => $tx->expired_at,
        ]);
    }
}
