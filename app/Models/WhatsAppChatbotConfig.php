<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WhatsAppChatbotConfig extends Model
{
    protected $table = 'whatsapp_chatbot_configs';
    protected $fillable = [
        'tenant_id', 'phone_number', 'webhook_token', 'status',
        'auto_replies', 'settings',
    ];

    protected $casts = [
        'auto_replies' => 'array',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->webhook_token)) {
                $model->webhook_token = Str::random(32);
            }
        });
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function messages() { return $this->hasMany(WhatsAppMessage::class); }

    /**
     * Find chatbot by webhook token
     */
    public static function findByWebhookToken(string $token): ?static
    {
        return static::where('webhook_token', $token)->first();
    }

    /**
     * Get auto-reply for keyword
     */
    public function getAutoReply(string $keyword): ?string
    {
        if (!$this->auto_replies) return null;
        
        foreach ($this->auto_replies as $rule) {
            if (mb_stripos($keyword, $rule['keyword']) !== false) {
                return $rule['response'];
            }
        }
        return null;
    }
}
