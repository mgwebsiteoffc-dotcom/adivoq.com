<?php
// app/Models/RevenueSplit.php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueSplit extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_id',
        'team_member_id',
        'split_type',
        'split_value',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'split_value' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}