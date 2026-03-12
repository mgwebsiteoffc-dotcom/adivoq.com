<?php
// app/Models/TaxSetting.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'financial_year',
        'pan_number',
        'gstin',
        'gst_registered',
        'gst_rate',
        'state_code',
        'state_name',
        'tds_default_rate',
        'business_type',
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
        'bank_branch',
        'upi_id',
    ];

    protected $casts = [
        'gst_registered' => 'boolean',
        'gst_rate' => 'decimal:2',
        'tds_default_rate' => 'decimal:2',
    ];
}