<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceActivity extends Model
{
    public $timestamps = false;

    protected $fillable = ['invoice_id', 'user_id', 'action', 'description', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function user() { return $this->belongsTo(User::class); }
}