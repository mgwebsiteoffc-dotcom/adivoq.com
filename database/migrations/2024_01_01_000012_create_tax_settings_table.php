<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('pan_number', 10)->nullable();
            $table->string('gstin', 15)->nullable();
            $table->boolean('gst_registered')->default(false);
            $table->string('state_code', 5)->nullable();
            $table->decimal('default_cgst_rate', 5, 2)->default(9);
            $table->decimal('default_sgst_rate', 5, 2)->default(9);
            $table->decimal('default_igst_rate', 5, 2)->default(18);
            $table->decimal('default_tds_rate', 5, 2)->default(10);
            $table->unsignedTinyInteger('financial_year_start_month')->default(4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_settings');
    }
};