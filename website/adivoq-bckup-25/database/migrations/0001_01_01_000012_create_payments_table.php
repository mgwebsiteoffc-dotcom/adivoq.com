<?php
// database/migrations/0001_01_01_000012_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('milestone_id')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('payment_method')->default('bank_transfer');
            // bank_transfer, upi, razorpay, stripe, paypal, cash, cheque
            $table->date('payment_date');
            $table->string('transaction_id')->nullable(); // Bank reference
            $table->string('gateway_payment_id')->nullable(); // Razorpay/Stripe ID
            $table->decimal('tds_deducted', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->string('status')->default('confirmed');
            // pending, confirmed, failed, refunded
            $table->timestamps();

            $table->foreign('milestone_id')
                  ->references('id')
                  ->on('milestones')
                  ->nullOnDelete();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'brand_id']);
            $table->index(['tenant_id', 'payment_date']);
            $table->index('gateway_payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};