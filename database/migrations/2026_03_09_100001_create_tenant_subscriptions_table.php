<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            $table->enum('plan', ['starter','professional','enterprise']);
            $table->string('gateway')->default('razorpay');

            $table->string('razorpay_customer_id')->nullable()->index();
            $table->string('razorpay_subscription_id')->nullable()->unique();

            $table->enum('status', ['created','authenticated','active','paused','cancelled','expired','failed'])->default('created');

            $table->timestamp('current_start_at')->nullable();
            $table->timestamp('current_end_at')->nullable();

            $table->string('last_payment_id')->nullable();
            $table->json('raw')->nullable();

            $table->timestamps();

            $table->index(['tenant_id','plan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};