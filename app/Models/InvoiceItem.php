<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price',
        'amount', 'hsn_sac_code', 'tenant_service_id', 'tax_rate', 'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
            
            // Auto-populate from service if selected
            if ($item->tenant_service_id && !$item->isDirty(['unit_price', 'tax_rate'])) {
                $service = TenantService::find($item->tenant_service_id);
                if ($service) {
                    if (!$item->isDirty('unit_price')) {
                        $item->unit_price = $service->default_unit_price;
                    }
                    if (!$item->isDirty('tax_rate')) {
                        $item->tax_rate = $service->tax_rate;
                    }
                }
            }
        });
    }

    public function invoice() 
    { 
        return $this->belongsTo(Invoice::class); 
    }

    /**
     * Get the tenant service associated with this line item.
     */
    public function service()
    {
        return $this->belongsTo(TenantService::class, 'tenant_service_id');
    }

    /**
     * Get the HSN code - from service or direct field.
     */
    public function getHsnCodeAttribute()
    {
        if ($this->service) {
            return $this->service->hsnCode->code ?? $this->hsn_sac_code;
        }
        return $this->hsn_sac_code;
    }

    /**
     * Get description - from service or direct field.
     */
    public function getDisplayDescriptionAttribute()
    {
        if ($this->service) {
            return $this->service->name;
        }
        return $this->description;
    }
}
