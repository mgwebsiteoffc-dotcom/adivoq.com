<?php
// app/Models/Brand.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'gstin',
        'pan',
        'payment_terms',
        'notes',
        'status',
    ];

    protected $casts = [
        'address' => 'array',
    ];

    // Relationships
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helpers
    public function totalRevenue()
    {
        return $this->payments()->where('status', 'confirmed')->sum('amount');
    }

    public function outstandingAmount()
    {
        return $this->invoices()->where('status', '!=', 'paid')->sum('balance_due');
    }
}