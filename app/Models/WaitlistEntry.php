<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistEntry extends Model
{
    protected $fillable = [
        'email', 'name', 'creator_type', 'status', 'invited_at', 'converted_at', 'notes',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'converted_at' => 'datetime',
    ];
}