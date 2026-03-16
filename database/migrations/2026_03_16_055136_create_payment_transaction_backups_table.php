<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transaction_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->unique()->constrained('payment_transactions');
            $table->foreignId('payment_order_id')->constrained('payment_orders');

            $table->string('reff', 50);
            $table->string('status', 20);

            $table->string('source', 20);

            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestampTz('transacted_at');
            $table->bigInteger('amount');
            $table->string('customer_name', 150);
            $table->string('code', 30);
            $table->timestampTz('expired_at');

            $table->timestampTz('backed_up_at')->useCurrent();

            $table->index('reff');
            $table->index('payment_order_id');
            $table->index('backed_up_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transaction_backups');
    }
};
