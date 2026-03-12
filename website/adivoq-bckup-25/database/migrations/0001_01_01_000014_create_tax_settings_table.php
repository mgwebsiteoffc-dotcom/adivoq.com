<?php
// database/migrations/0001_01_01_000014_create_tax_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('financial_year')->default('2025-26');
            $table->string('pan_number', 10)->nullable();
            $table->string('gstin', 15)->nullable();
            $table->boolean('gst_registered')->default(false);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->string('state_code', 5)->nullable(); // For CGST/SGST vs IGST
            $table->string('state_name')->nullable();
            $table->decimal('tds_default_rate', 5, 2)->default(10.00);
            $table->string('business_type')->default('individual');
            // individual, sole_proprietor, partnership, llp, company

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc', 11)->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('upi_id')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'financial_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_settings');
    }
};