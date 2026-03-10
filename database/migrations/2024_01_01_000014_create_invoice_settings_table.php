<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_prefix', 20)->default('INV');
            $table->unsignedInteger('next_invoice_number')->default(1);
            $table->string('default_payment_terms', 20)->default('net_30');
            $table->unsignedInteger('default_payment_terms_days')->default(30);
            $table->text('default_notes')->nullable();
            $table->text('default_terms_and_conditions')->nullable();
            $table->string('invoice_color', 7)->default('#4F46E5');
            $table->boolean('show_logo')->default(true);
            $table->string('template', 20)->default('default');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};