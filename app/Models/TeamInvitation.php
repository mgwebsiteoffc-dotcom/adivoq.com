<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    protected $fillable = [
        'tenant_id', 'email', 'role', 'token', 'invited_by', 'accepted_at', 'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function invitedBy() { return $this->belongsTo(User::class, 'invited_by'); }

    public function isExpired(): bool { return $this->expires_at->isPast(); }
    public function isAccepted(): bool { return $this->accepted_at !== null; }
    public function isPending(): bool { return !$this->isAccepted() && !$this->isExpired(); }
}