<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'invoice_id', 'amount', 'payment_date', 'payment_method',
        'transaction_reference', 'gateway_payment_id', 'gateway_order_id',
        'gateway_signature', 'receipt_path', 'notes', 'status', 'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function scopeConfirmed($query) { return $query->where('status', 'confirmed'); }
    public function scopeThisMonth($query) {
        return $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year);
    }
}