<?php

namespace App\Services;

use App\Models\TrackingKey;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    /**
     * Track an event from a pixel/key
     */
    public function track(string $key, string $eventName, array $data = [], string $sessionId = null): bool
    {
        try {
            $trackingKey = TrackingKey::where('key', $key)->first();
            
            if (!$trackingKey || !$trackingKey->canTrack()) {
                return false;
            }

            $data['ip_address'] = request()->ip();
            $data['user_agent'] = request()->userAgent();
            $data['source_url'] = $data['source_url'] ?? request()->header('referer');

            $trackingKey->logEvent($eventName, $data, $sessionId);

            Log::info('Tracking event recorded', [
                'key' => $key,
                'event' => $eventName,
                'tenant_id' => $trackingKey->tenant_id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Tracking error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get tracking key by ID for tenant
     */
    public function getKeyForTenant(int $tenantId, int $keyId): ?TrackingKey
    {
        return TrackingKey::where('tenant_id', $tenantId)
            ->where('id', $keyId)
            ->first();
    }

    /**
     * Create a new tracking key
     */
    public function createKey(int $tenantId, array $data): TrackingKey
    {
        return TrackingKey::create([
            'tenant_id' => $tenantId,
            'brand_id' => $data['brand_id'] ?? null,
            'name' => $data['name'] ?? 'Tracking Key',
            'type' => $data['type'] ?? 'pixel',
            'config' => $data['config'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'monthly_limit' => $data['monthly_limit'] ?? 0,
        ]);
    }

    /**
     * Get tracking stats for a key
     */
    public function getStats(TrackingKey $key, $days = 30)
    {
        $since = now()->subDays($days);

        return [
            'total_events' => $key->events()->where('created_at', '>=', $since)->count(),
            'unique_sessions' => $key->events()->where('created_at', '>=', $since)->distinct('session_id')->count(),
            'events_by_name' => $key->events()
                ->where('created_at', '>=', $since)
                ->groupBy('event_name')
                ->selectRaw('event_name, count(*) as count')
                ->pluck('count', 'event_name'),
            'monthly_remaining' => max(0, $key->monthly_limit - $key->monthly_events),
        ];
    }
}
