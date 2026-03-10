<?php

namespace App\Services;

use App\Models\PaymentGatewaySetting;
use Illuminate\Support\Facades\Crypt;
use Razorpay\Api\Api;
use Exception;

class RazorpayService
{
    public function apiForTenant(int $tenantId): Api
    {
        $settings = PaymentGatewaySetting::where('tenant_id', $tenantId)->first();

        if (!$settings || !$settings->razorpay_enabled || !$settings->razorpay_key_id || !$settings->razorpay_key_secret) {
            throw new Exception('Razorpay is not configured for this tenant.');
        }

        $secret = $settings->razorpay_key_secret;

        try { $secret = Crypt::decryptString($secret); } catch (\Throwable $e) {}

        return new Api($settings->razorpay_key_id, $secret);
    }

    public function webhookSecretForTenant(int $tenantId): ?string
    {
        $settings = PaymentGatewaySetting::where('tenant_id', $tenantId)->first();
        if (!$settings?->razorpay_webhook_secret) return null;

        $secret = $settings->razorpay_webhook_secret;
        try { $secret = Crypt::decryptString($secret); } catch (\Throwable $e) {}
        return $secret;
    }

    public function keyIdForTenant(int $tenantId): string
    {
        return PaymentGatewaySetting::where('tenant_id', $tenantId)->value('razorpay_key_id') ?? '';
    }

    public function getKeyId(int $tenantId): string
{
    return $this->keyIdForTenant($tenantId);
}
}