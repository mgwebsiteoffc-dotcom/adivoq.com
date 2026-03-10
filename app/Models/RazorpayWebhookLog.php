<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RazorpayWebhookLog extends Model
{
    protected $fillable = [
        'tenant_id','event','gateway_order_id','gateway_payment_id',
        'signature_valid','status','message','payload'
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'payload' => 'array',
    ];
}