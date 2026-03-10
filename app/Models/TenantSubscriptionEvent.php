<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscriptionEvent extends Model
{
    protected $fillable = [
        'tenant_id','event','razorpay_subscription_id','razorpay_payment_id',
        'signature_valid','status','message','payload'
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'payload' => 'array',
    ];
}