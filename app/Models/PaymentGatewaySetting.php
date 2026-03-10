<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'tenant_id', 'razorpay_key_id', 'razorpay_key_secret', 'razorpay_enabled',
        'stripe_publishable_key', 'stripe_secret_key', 'stripe_enabled',
    ];

    protected $casts = [
        'razorpay_enabled' => 'boolean',
        'stripe_enabled' => 'boolean',
    ];

    protected $hidden = ['razorpay_key_secret', 'stripe_secret_key'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}