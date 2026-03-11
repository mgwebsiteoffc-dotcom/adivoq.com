<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingKey extends Model
{
    protected $fillable = [
        'tenant_id', 'brand_id', 'key', 'name', 'type',
        'config', 'is_active', 'monthly_events', 'monthly_limit',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function events() { return $this->hasMany(TrackingEvent::class); }

    // Boot - auto-generate key
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->key)) {
                $model->key = 'tk_' . Str::random(32);
            }
        });
    }

    /**
     * Check if key can track (rate limiting)
     */
    public function canTrack(): bool
    {
        if (!$this->is_active) return false;
        if ($this->monthly_limit === 0) return true;
        return $this->monthly_events < $this->monthly_limit;
    }

    /**
     * Log a tracking event
     */
    public function logEvent(string $eventName, array $data = [], string $sessionId = null): TrackingEvent
    {
        $this->increment('monthly_events');

        return $this->events()->create([
            'tenant_id' => $this->tenant_id,
            'event_name' => $eventName,
            'event_data' => $data,
            'source_url' => $data['source_url'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'session_id' => $sessionId,
        ]);
    }
}
