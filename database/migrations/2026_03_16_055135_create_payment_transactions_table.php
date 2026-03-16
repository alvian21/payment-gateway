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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_order_id')->constrained('payment_orders')->cascadeOnDelete();
            $table->string('reff', 50);

            $table->string('status', 20);

            $table->string('source', 20)->default('api');

            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestampTz('transacted_at')->useCurrent();

            $table->bigInteger('amount');
            $table->string('customer_name', 150);
            $table->string('code', 30);
            $table->timestampTz('expired_at');

            $table->timestampsTz();

            $table->index('payment_order_id');
            $table->index('reff');
            $table->index('status');
            $table->index('transacted_at');
        });

        \Illuminate\Support\Facades\DB::statement("CREATE UNIQUE INDEX uq_payment_transactions_paid_once ON payment_transactions (payment_order_id) WHERE status = 'paid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
