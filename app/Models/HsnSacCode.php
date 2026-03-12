<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsnSacCode extends Model
{
    protected $table = 'hsn_sac_codes';

    protected $fillable = [
        'code',
        'slug',
        'description',
        'applicable_to',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the route key for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get services using this HSN code.
     */
    public function services()
    {
        return $this->hasMany(TenantService::class, 'hsn_sac_code_id');
    }

    /**
     * Get invoice items using this HSN code.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'hsn_sac_code_id');
    }

    /**
     * Scope: Filter by applicable type
     */
    public function scopeApplicableTo($query, $type)
    {
        return $query->whereIn('applicable_to', [$type, 'Both']);
    }
}
