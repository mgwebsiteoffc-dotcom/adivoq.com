<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'phone', 'contact_person', 'website',
        'logo', 'address_line1', 'address_line2', 'city', 'state', 'state_code',
        'pincode', 'country', 'gstin', 'pan_number', 'notes', 'status',
    ];

    public function campaigns() { return $this->hasMany(Campaign::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }

    public function totalRevenue()
    {
        return $this->invoices()->where('status', 'paid')->sum('total_amount');
    }

    public function totalOutstanding()
    {
        return $this->invoices()->whereIn('status', ['sent', 'overdue', 'partially_paid'])->sum('amount_due');
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address_line1, $this->address_line2, $this->city, $this->state, $this->pincode, $this->country])->filter()->implode(', ');
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}