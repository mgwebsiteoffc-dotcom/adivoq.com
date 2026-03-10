<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    protected $fillable = [
        'tenant_id','plan','gateway',
        'razorpay_customer_id','razorpay_subscription_id',
        'status','current_start_at','current_end_at',
        'last_payment_id','raw'
    ];

    protected $casts = [
        'raw' => 'array',
        'current_start_at' => 'datetime',
        'current_end_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}