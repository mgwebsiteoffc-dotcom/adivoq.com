<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'tenant_id', 'invoice_prefix', 'next_invoice_number',
        'default_payment_terms', 'default_payment_terms_days',
        'default_notes', 'default_terms_and_conditions',
        'invoice_color', 'show_logo', 'template',
    ];

    protected $casts = ['show_logo' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public function generateInvoiceNumber(): string
    {
        $number = $this->invoice_prefix . '-' . str_pad($this->next_invoice_number, 5, '0', STR_PAD_LEFT);
        $this->increment('next_invoice_number');
        return $number;
    }
}