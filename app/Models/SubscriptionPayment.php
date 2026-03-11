<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SubscriptionPayment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'tenant_subscription_id',
        'razorpay_payment_id',
        'razorpay_subscription_id',
        'amount',
        'currency',
        'status',
        'payment_date',
        'plan',
        'raw'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'raw' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription()
    {
        return $this->belongsTo(TenantSubscription::class, 'tenant_subscription_id');
    }

    public function isCaptured()
    {
        return $this->status === 'captured';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isRefunded()
    {
        return $this->status === 'refunded';
    }
}
