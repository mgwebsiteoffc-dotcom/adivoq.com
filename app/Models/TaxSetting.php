<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $fillable = [
        'tenant_id', 'pan_number', 'gstin', 'gst_registered', 'state_code',
        'default_cgst_rate', 'default_sgst_rate', 'default_igst_rate',
        'default_tds_rate', 'financial_year_start_month',
    ];

    protected $casts = [
        'gst_registered' => 'boolean',
        'default_cgst_rate' => 'decimal:2',
        'default_sgst_rate' => 'decimal:2',
        'default_igst_rate' => 'decimal:2',
        'default_tds_rate' => 'decimal:2',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}