<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        if (session()->has('success')) {
            session()->keep(['success']);
        }

        // pull stored values from DB and merge with defaults
        $stored = \App\Models\SystemSetting::allSettings();

        $settings = [
            'site_name' => $stored['site_name'] ?? config('app.name'),
            'default_currency' => $stored['default_currency'] ?? config('invoicehero.default_currency'),
            'default_gst_rate' => $stored['default_gst_rate'] ?? config('invoicehero.default_gst_rate'),
            'maintenance_mode' => isset($stored['maintenance_mode']) ? (bool) $stored['maintenance_mode'] : app()->isDownForMaintenance(),
            'smtp_host' => $stored['smtp_host'] ?? config('mail.mailers.smtp.host'),
            'smtp_port' => $stored['smtp_port'] ?? config('mail.mailers.smtp.port'),
            'smtp_username' => $stored['smtp_username'] ?? config('mail.mailers.smtp.username'),
            'from_address' => $stored['from_address'] ?? config('mail.from.address'),
            'whatify_api_key' => $stored['whatify_api_key'] ?? config('services.whatify.api_key'),
            'whatify_base_url' => $stored['whatify_base_url'] ?? config('services.whatify.base_url'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'site_name',
            'default_currency',
            'default_gst_rate',
            'maintenance_mode',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'from_address',
            'whatify_api_key',
            'whatify_base_url',
        ];

        foreach ($keys as $key) {
            if ($key === 'maintenance_mode') {
                $value = $request->boolean('maintenance_mode');
                \App\Models\SystemSetting::set($key, $value);
                continue;
            }

            if ($request->has($key)) {
                $value = $request->input($key);
                \App\Models\SystemSetting::set($key, $value);
                // apply immediately to config as well
                config()->set($this->configPathForKey($key), $value);
            }
        }

        // handle maintenance mode toggling
        if ($request->boolean('maintenance_mode') && !app()->isDownForMaintenance()) {
            \Artisan::call('down');
        } elseif (!$request->boolean('maintenance_mode') && app()->isDownForMaintenance()) {
            \Artisan::call('up');
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Map form keys to config paths for runtime updates.
     */
    protected function configPathForKey(string $key): string
    {
        return match ($key) {
            'site_name' => 'app.name',
            'default_currency' => 'invoicehero.default_currency',
            'default_gst_rate' => 'invoicehero.default_gst_rate',
            'smtp_host' => 'mail.mailers.smtp.host',
            'smtp_port' => 'mail.mailers.smtp.port',
            'smtp_username' => 'mail.mailers.smtp.username',
            'from_address' => 'mail.from.address',
            'whatify_api_key' => 'services.whatify.api_key',
            'whatify_base_url' => 'services.whatify.base_url',
            default => '',
        };
    }

    /**
     * Test WhatsApp integration
     */
    public function testWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $message = $request->input('message');

        // Get settings from database or .env (bypass config cache)
        $stored = \App\Models\SystemSetting::allSettings();
        
        $apiKey = $stored['whatify_api_key'] ?? env('WHATIFY_API_KEY');
        $baseUrl = rtrim($stored['whatify_base_url'] ?? env('WHATIFY_BASE_URL', 'https://whatify.in'), '/');

        // Log the test attempt
        \Illuminate\Support\Facades\Log::info('WhatsApp Test Started', [
            'phone' => $phone,
            'api_key_set' => !empty($apiKey),
            'base_url' => $baseUrl,
        ]);

        try {
            // Use the WhatsApp service with fresh credentials
            $whatsappService = new \App\Services\WhatsAppService();
            $result = $whatsappService->sendMessage($phone, $message);

            // Log result
            \Illuminate\Support\Facades\Log::info('WhatsApp Test Result', $result);

            return response()->json($result);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp Test Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
            ], 500);
        }
    }
}
