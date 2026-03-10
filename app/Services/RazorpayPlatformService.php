<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayPlatformService
{
    public function api(): Api
    {
        return new Api(
            config('services.razorpay_platform.key_id'),
            config('services.razorpay_platform.key_secret')
        );
    }

    public function keyId(): string
    {
        return (string) config('services.razorpay_platform.key_id');
    }

    public function planId(string $planKey): ?string
    {
        return config("services.razorpay_platform.plans.$planKey");
    }

    public function webhookSecret(): ?string
    {
        return config('services.razorpay_platform.webhook_secret');
    }
}