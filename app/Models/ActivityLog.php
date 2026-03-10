<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'loggable_type', 'loggable_id', 'tenant_id', 'action',
        'description', 'ip_address', 'user_agent', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function loggable() { return $this->morphTo(); }
    public function tenant() { return $this->belongsTo(Tenant::class); }

    public static function record(Model $user, string $action, string $description = null, array $metadata = []): self
    {
        return static::create([
            'loggable_type' => get_class($user),
            'loggable_id' => $user->id,
            'tenant_id' => $user->tenant_id ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata ?: null,
        ]);
    }
}