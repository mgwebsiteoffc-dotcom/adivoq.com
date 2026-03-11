<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedEvent extends Model
{
    protected $fillable = [
        'tenant_id',
        'event_name',
        'event_category',
        'event_data',
        'page_url',
        'referrer',
        'user_agent',
        'ip_address',
        'session_id',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
