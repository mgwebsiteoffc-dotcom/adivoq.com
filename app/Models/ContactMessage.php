<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'status', 'replied_at', 'admin_notes'];

    protected $casts = ['replied_at' => 'datetime'];

    public function scopeUnread($query) { return $query->where('status', 'unread'); }
}