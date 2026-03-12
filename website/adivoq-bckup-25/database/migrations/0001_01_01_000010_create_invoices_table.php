<?php
// database/migrations/0001_01_01_000010_create_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();

            // Invoice Identity
            $table->string('invoice_number');
            $table->string('reference_number')->nullable(); // PO number from brand
            $table->string('public_token', 64)->unique(); // For public viewing URL

            // Dates
            $table->date('issue_date');
            $table->date('due_date');

            // Amounts
            $table->decimal('subtotal', 12, 2)->default(0);

            // Discount
            $table->string('discount_type')->nullable(); // percentage, fixed
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);

            // Taxable Amount (after discount)
            $table->decimal('taxable_amount', 12, 2)->default(0);

            // GST (India) — Same State
            $table->decimal('cgst_rate', 5, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_rate', 5, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);

            // GST (India) — Different State
            $table->decimal('igst_rate', 5, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);

            // Total Tax
            $table->decimal('tax_amount', 12, 2)->default(0);

            // TDS (Tax Deducted at Source by Brand)
            $table->decimal('tds_rate', 5, 2)->default(0);
            $table->decimal('tds_amount', 12, 2)->default(0);

            // Final Amounts
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2)->default(0);

            // Currency
            $table->string('currency', 10)->default('INR');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);

            // Status
            $table->string('status')->default('draft');
            // draft, sent, viewed, paid, partially_paid, overdue, cancelled

            // Content
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();

            // Payment
            $table->string('payment_link')->nullable();

            // Tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // File
            $table->string('pdf_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['tenant_id', 'invoice_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'brand_id']);
            $table->index(['tenant_id', 'due_date']);
            $table->index('public_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};