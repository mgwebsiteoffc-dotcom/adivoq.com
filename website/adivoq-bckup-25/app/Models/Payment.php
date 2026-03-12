<?php
// app/Models/Payment.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'brand_id',
        'campaign_id',
        'milestone_id',
        'amount',
        'currency',
        'payment_method',
        'payment_date',
        'transaction_id',
        'gateway_payment_id',
        'tds_deducted',
        'notes',
        'receipt_url',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tds_deducted' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function revenueSplits()
    {
        return $this->hasMany(RevenueSplit::class);
    }
}