<?php
// app/Models/Milestone.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'title',
        'description',
        'amount',
        'due_date',
        'completed_at',
        'status',
        'deliverable_url',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helpers
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->status !== 'completed' && $this->due_date && $this->due_date->isPast();
    }
}