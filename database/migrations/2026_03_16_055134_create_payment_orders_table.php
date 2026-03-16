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
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reff', 50)->unique();
            $table->string('customer_name', 150);
            $table->string('hp', 20);
            $table->string('code', 30);

            $table->bigInteger('base_amount');
            $table->bigInteger('fee')->default(2500);
            $table->bigInteger('amount');

            $table->timestampTz('expired_at');
            $table->timestampTz('paid_at')->nullable();

            $table->string('status', 20)->default('pending');

            $table->foreignId('flagged_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('flagged_at')->nullable();

            $table->timestampsTz();

            $table->index(['status', 'created_at']);
            $table->index('expired_at');
            $table->index('paid_at');
            $table->index('hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
