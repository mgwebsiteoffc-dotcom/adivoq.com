<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_subscription_id')->constrained()->cascadeOnDelete();
            $table->string('razorpay_payment_id')->unique();
            $table->string('razorpay_subscription_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('pending'); // pending, captured, failed, refunded
            $table->timestamp('payment_date');
            $table->string('plan')->nullable(); // plan at time of payment
            $table->json('raw')->nullable(); // full Razorpay response
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_subscription_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
