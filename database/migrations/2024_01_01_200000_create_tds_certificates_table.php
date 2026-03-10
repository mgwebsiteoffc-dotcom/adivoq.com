<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tds_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();

            $table->string('certificate_number')->nullable();
            $table->string('financial_year', 9)->nullable(); // e.g. 2024-25
            $table->enum('quarter', ['Q1','Q2','Q3','Q4'])->nullable();

            $table->decimal('tds_amount', 12, 2)->default(0);
            $table->date('deducted_at')->nullable();

            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending','verified'])->default('pending');

            $table->timestamps();

            $table->index(['tenant_id','financial_year','quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tds_certificates');
    }
};