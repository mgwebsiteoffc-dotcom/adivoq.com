<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestWhatsApp extends Command
{
    protected $signature = 'whatsapp:test {phone} {message?}';
    protected $description = 'Test WhatsApp API integration';

    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Hello! This is a test message from InvoiceHero.';
        
        $this->info('Testing WhatsApp API...');
        $this->line('');
        
        // Get credentials from env
        $baseUrl = rtrim(env('WHATIFY_BASE_URL', 'https://whatify.in'), '/');
        $apiKey = env('WHATIFY_API_KEY');
        
        $this->info("📋 Configuration:");
        $this->line("  Base URL: {$baseUrl}");
        $this->line("  API Key: " . (strlen($apiKey) > 10 ? substr($apiKey, 0, 10) . '...' : '***'));
        $this->line("  Phone: {$phone}");
        $this->line("  Message: {$message}");
        $this->line('');
        
        // Format phone number
        $formattedPhone = $this->formatPhoneE164($phone);
        if (!$formattedPhone) {
            $this->error("❌ Invalid phone number format!");
            return 1;
        }
        
        $this->info("✅ Phone formatted to: {$formattedPhone}");
        $this->line('');
        
        // Build endpoint
        $endpoint = $baseUrl . '/api/send';
        $this->info("🔗 Endpoint: {$endpoint}");
        $this->line('');
        
        // Make the request
        $this->info("⏳ Sending request...");
        $this->line('');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(15)->post($endpoint, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            $statusCode = $response->status();
            $body = $response->json();

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("📊 Response Status: {$statusCode}");
            $this->line("Response Body:");
            $this->line(json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line('');

            if ($statusCode === 200 || $statusCode === 201) {
                $this->info("✅ SUCCESS! Message sent successfully!");
                return 0;
            } else {
                $this->error("❌ FAILED! Status code: {$statusCode}");
                if (is_array($body)) {
                    if (isset($body['message'])) {
                        $this->error("   Message: " . $body['message']);
                    }
                    if (isset($body['error'])) {
                        $this->error("   Error: " . $body['error']);
                    }
                }
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            Log::error('WhatsApp test command exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }

    /**
     * Format phone number to E.164 format
     */
    protected function formatPhoneE164(string $phone): ?string
    {
        $phone = trim($phone);
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        if (strpos($cleaned, '+') === 0) {
            return $cleaned;
        }
        
        if (strlen($cleaned) === 10) {
            return '+91' . $cleaned;
        }
        
        if (strlen($cleaned) === 12 && strpos($cleaned, '91') === 0) {
            return '+' . $cleaned;
        }
        
        if (strlen($cleaned) === 11 && strpos($cleaned, '1') === 0) {
            return '+' . $cleaned;
        }
        
        if (strlen($cleaned) > 10 && strlen($cleaned) < 20) {
            return '+' . $cleaned;
        }
        
        return null;
    }
}
