<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringInvoice extends Model
{
    protected $fillable = [
        'brand_id',
        'campaign_id',
        'title',
        'amount',
        'frequency',
        'start_date',
        'next_run_date',
        'active',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}