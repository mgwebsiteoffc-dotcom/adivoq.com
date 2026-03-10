<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('email_on_invoice_sent')->default(true);
            $table->boolean('email_on_payment_received')->default(true);
            $table->boolean('email_on_invoice_overdue')->default(true);
            $table->boolean('whatsapp_on_invoice_sent')->default(false);
            $table->boolean('whatsapp_on_payment_received')->default(false);
            $table->boolean('whatsapp_on_invoice_overdue')->default(false);
            $table->unsignedTinyInteger('reminder_days_before_due')->default(3);
            $table->enum('reminder_frequency', ['once', 'daily', 'weekly'])->default('once');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};