<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'is_system_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_system_admin' => 'boolean',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helpers
    public function isSuperAdmin()
    {
        return $this->is_system_admin === true;
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function canManageInvoices()
    {
        return in_array($this->role, ['owner', 'manager', 'accountant']);
    }

    public function canManageBrands()
    {
        return in_array($this->role, ['owner', 'manager', 'editor']);
    }
}