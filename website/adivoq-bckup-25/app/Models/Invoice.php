<?php
// app/Models/Invoice.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'brand_id',
        'campaign_id',
        'invoice_number',
        'reference_number',
        'public_token',
        'issue_date',
        'due_date',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'taxable_amount',
        'cgst_rate',
        'cgst_amount',
        'sgst_rate',
        'sgst_amount',
        'igst_rate',
        'igst_amount',
        'tax_amount',
        'tds_rate',
        'tds_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'currency',
        'exchange_rate',
        'status',
        'notes',
        'terms_and_conditions',
        'payment_link',
        'sent_at',
        'viewed_at',
        'paid_at',
        'pdf_path',
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
        'tax_amount' => 'decimal:2',
        'tds_rate' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helpers
    public function isPaid()
    {
        return $this->status === 'paid';
    }

public function isOverdue()
{
    if ($this->status === 'paid') {
        return false;
    }

    if (!$this->due_date) {
        return false;
    }

    return now()->greaterThan($this->due_date);
}

    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            'paid' => 'badge-success',
            'partially_paid' => 'badge-info',
            'overdue' => 'badge-danger',
            'sent' => 'badge-info',
            'viewed' => 'badge-info',
            'draft' => 'badge-neutral',
            'cancelled' => 'badge-danger',
            default => 'badge-neutral',
        };
    }
    
    public function getDisplayStatusAttribute()
{
    if ($this->isOverdue()) {
        return 'overdue';
    }

    return $this->status;
}
}