<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantService extends Model
{
    use BelongsToTenant;

    protected $table = 'tenant_services';

    protected $fillable = [
        'tenant_id',
        'hsn_sac_code_id',
        'name',
        'description',
        'default_unit_price',
        'unit',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'default_unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns this service.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the HSN/SAC code for this service.
     */
    public function hsnCode()
    {
        return $this->belongsTo(HsnSacCode::class, 'hsn_sac_code_id');
    }

    /**
     * Get invoice items using this service.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'tenant_service_id');
    }

    /**
     * Scope: Only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the HSN code with description formatted for display.
     */
    public function getHsnDisplay()
    {
        $hsn = $this->hsnCode;
        if (!$hsn) {
            return 'N/A';
        }
        return "{$hsn->code} - {$hsn->description}";
    }
}
