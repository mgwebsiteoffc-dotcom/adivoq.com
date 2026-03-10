<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'phone', 'avatar',
        'role', 'revenue_split_percentage', 'status', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'revenue_split_percentage' => 'decimal:2',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function invoiceActivities() { return $this->hasMany(InvoiceActivity::class); }

    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isManager(): bool { return in_array($this->role, ['owner', 'manager']); }
    public function isAccountant(): bool { return in_array($this->role, ['owner', 'manager', 'accountant']); }
    public function isEditor(): bool { return in_array($this->role, ['owner', 'manager', 'editor']); }
    public function isViewer(): bool { return true; }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles);
    }

    public function canManageFinances(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'accountant']);
    }

    public function canManageCampaigns(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'editor']);
    }

    public function canManageTeam(): bool
    {
        return in_array($this->role, ['owner', 'manager']);
    }
}