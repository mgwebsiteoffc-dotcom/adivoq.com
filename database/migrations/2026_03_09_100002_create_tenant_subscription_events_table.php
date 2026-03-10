<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();

            $table->string('event')->nullable()->index();
            $table->string('razorpay_subscription_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->boolean('signature_valid')->default(false);

            $table->enum('status', ['processed','ignored','error'])->default('ignored');
            $table->string('message')->nullable();
            $table->json('payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscription_events');
    }
};