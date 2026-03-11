<?php

namespace App\Services;

use App\Models\WhatsAppChatbotConfig;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChatbotService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.whatify.api_key');
        $this->baseUrl = config('services.whatify.base_url', 'https://whatify.in/api');
    }

    /**
     * Handle incoming WhatsApp message
     */
    public function handleIncomingMessage(WhatsAppChatbotConfig $config, string $phone, string $message): void
    {
        try {
            // Store inbound message
            $stored = $config->messages()->create([
                'contact_phone' => $phone,
                'message' => $message,
                'direction' => 'inbound',
                'status' => 'delivered',
            ]);

            // Try to get auto-reply
            $autoReply = $config->getAutoReply($message);
            
            if ($autoReply) {
                $this->sendMessage($config, $phone, $autoReply, $message);
            } else {
                // Log as unanswered inquiry for manual follow-up
                Log::info('WhatsApp message requires manual response', [
                    'config_id' => $config->id,
                    'phone' => $phone,
                    'message' => $message,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp handle error: ' . $e->getMessage(), [
                'config_id' => $config->id,
                'phone' => $phone,
            ]);
        }
    }

    /**
     * Send WhatsApp message via Whatify
     */
    public function sendMessage(WhatsAppChatbotConfig $config, string $phone, string $message, string $inReplyTo = null): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/send', [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store outbound message
                $config->messages()->create([
                    'contact_phone' => $phone,
                    'message' => $message,
                    'direction' => 'outbound',
                    'status' => 'sent',
                    'external_id' => $data['id'] ?? null,
                ]);

                return true;
            }

            Log::error('WhatsApp send failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get chatbot conversation history
     */
    public function getConversationHistory(WhatsAppChatbotConfig $config, string $phone, int $limit = 20)
    {
        return $config->messages()
            ->where('contact_phone', $phone)
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Update auto-reply rules
     */
    public function updateAutoReplies(WhatsAppChatbotConfig $config, array $rules): WhatsAppChatbotConfig
    {
        $config->update(['auto_replies' => $rules]);
        return $config;
    }
}
