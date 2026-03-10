<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role', 'avatar', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function blogPosts() { return $this->hasMany(BlogPost::class); }
    public function guides() { return $this->hasMany(Guide::class); }

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
}