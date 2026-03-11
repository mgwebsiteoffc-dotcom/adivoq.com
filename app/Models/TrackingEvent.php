<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    protected $fillable = [
        'tracking_key_id', 'tenant_id', 'event_name', 'event_data',
        'source_url', 'user_agent', 'ip_address', 'session_id',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function trackingKey() { return $this->belongsTo(TrackingKey::class); }
    public function tenant() { return $this->belongsTo(Tenant::class); }
}
