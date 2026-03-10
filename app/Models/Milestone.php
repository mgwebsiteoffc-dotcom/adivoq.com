<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'campaign_id', 'title', 'description', 'amount',
        'due_date', 'completed_at', 'status', 'sort_order', 'invoice_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function markComplete(): void
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }
}