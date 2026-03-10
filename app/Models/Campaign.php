<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'brand_id', 'name', 'description', 'platform',
        'campaign_type', 'total_amount', 'start_date', 'end_date',
        'status', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function milestones() { return $this->hasMany(Milestone::class)->orderBy('sort_order'); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function expenses() { return $this->hasMany(Expense::class); }

    public function revenueCollected()
    {
        return $this->invoices()->sum('amount_paid');
    }

    public function revenueProgress(): float
    {
        if ($this->total_amount <= 0) return 0;
        return min(100, round(($this->revenueCollected() / $this->total_amount) * 100, 1));
    }

    public function completedMilestones()
    {
        return $this->milestones()->where('status', 'completed')->count();
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}