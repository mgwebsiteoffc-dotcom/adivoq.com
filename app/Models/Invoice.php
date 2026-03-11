<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'brand_id', 'campaign_id', 'invoice_number',
        'reference_number', 'issue_date', 'due_date', 'status',
        'subtotal', 'discount_type', 'discount_value', 'discount_amount',
        'taxable_amount', 'cgst_rate', 'cgst_amount', 'sgst_rate', 'sgst_amount',
        'igst_rate', 'igst_amount', 'total_tax', 'total_amount',
        'tds_rate', 'tds_amount', 'net_receivable', 'amount_paid', 'amount_due',
        'currency', 'payment_terms', 'payment_terms_days',
        'notes', 'terms_and_conditions',
        'is_recurring', 'recurring_frequency', 'next_recurring_date', 'paused',
        'payment_link_token', 'payment_link_expires_at',
        'sent_at', 'viewed_at', 'paid_at', 'cancelled_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'cgst_rate' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_rate' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_rate' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tds_rate' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'net_receivable' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'is_recurring' => 'boolean',
        'paused' => 'boolean',
        'next_recurring_date' => 'date',
        'payment_link_expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function items() { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order'); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function activities() { return $this->hasMany(InvoiceActivity::class)->orderByDesc('created_at'); }
    public function milestones() { return $this->hasMany(Milestone::class); }

    // Generate unique payment link token
    public function generatePaymentLink(): string
    {
        $this->update([
            'payment_link_token' => Str::random(64),
            'payment_link_expires_at' => now()->addDays(30),
        ]);
        return route('payment.link', $this->payment_link_token);
    }

    // Calculate totals
    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('amount');
        $discountAmount = 0;

        if ($this->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = $this->discount_value;
        }

        $taxableAmount = $subtotal - $discountAmount;

        // Determine GST type (same state = CGST+SGST, diff state = IGST)
        $tenant = $this->tenant;
        $brand = $this->brand;
        $sameState = $tenant && $brand && $tenant->state_code && $brand->state_code
            && $tenant->state_code === $brand->state_code;

        $cgstAmount = 0; $sgstAmount = 0; $igstAmount = 0;

        if ($sameState) {
            $cgstAmount = $taxableAmount * ($this->cgst_rate / 100);
            $sgstAmount = $taxableAmount * ($this->sgst_rate / 100);
        } else {
            $igstAmount = $taxableAmount * ($this->igst_rate / 100);
        }

        $totalTax = $cgstAmount + $sgstAmount + $igstAmount;
        $totalAmount = $taxableAmount + $totalTax;
        $tdsAmount = $totalAmount * ($this->tds_rate / 100);
        $netReceivable = $totalAmount - $tdsAmount;

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'taxable_amount' => $taxableAmount,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'total_tax' => $totalTax,
            'total_amount' => $totalAmount,
            'tds_amount' => $tdsAmount,
            'net_receivable' => $netReceivable,
            'amount_due' => $netReceivable - $this->amount_paid,
        ]);
    }

    public function recordPayment(float $amount): void
    {
        $newPaid = $this->amount_paid + $amount;
        $newDue = $this->net_receivable - $newPaid;

        $status = 'partially_paid';
        $paidAt = null;

        if ($newDue <= 0) {
            $status = 'paid';
            $paidAt = now();
            $newDue = 0;
        }

        $this->update([
            'amount_paid' => $newPaid,
            'amount_due' => $newDue,
            'status' => $status,
            'paid_at' => $paidAt,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast()
            && in_array($this->status, ['sent', 'viewed', 'partially_paid']);
    }

    public function isDraft(): bool { return $this->status === 'draft'; }
    public function isPaid(): bool { return $this->status === 'paid'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function getCurrencySymbolAttribute(): string
    {
        return config("invoicehero.currencies.{$this->currency}.symbol", '₹');
    }

    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeOverdue($query) {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'viewed', 'partially_paid']);
    }
    public function scopeThisMonth($query) {
        return $query->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year);
    }
    public function scopeThisYear($query) {
        return $query->whereYear('issue_date', now()->year);
    }

    // Activity logging
    public function logActivity(string $action, string $description = null, array $metadata = []): void
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }
}