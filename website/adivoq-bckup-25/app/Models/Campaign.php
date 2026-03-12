<?php
// app/Models/Campaign.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'brand_id',
        'title',
        'description',
        'campaign_type',
        'platform',
        'total_value',
        'currency',
        'status',
        'start_date',
        'end_date',
        'contract_url',
        'notes',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Helpers
    public function completedMilestonesCount()
    {
        return $this->milestones()->where('status', 'completed')->count();
    }

    public function totalMilestonesCount()
    {
        return $this->milestones()->count();
    }

    public function progressPercentage()
    {
        $total = $this->totalMilestonesCount();
        return $total > 0 ? ($this->completedMilestonesCount() / $total) * 100 : 0;
    }
}