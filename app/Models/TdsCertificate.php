<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TdsCertificate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id','brand_id','invoice_id',
        'certificate_number','financial_year','quarter',
        'tds_amount','deducted_at','file_path','notes','status'
    ];

    protected $casts = [
        'tds_amount' => 'decimal:2',
        'deducted_at' => 'date',
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}