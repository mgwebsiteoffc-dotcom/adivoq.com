<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';
    protected $fillable = [
        'whatsapp_chatbot_config_id', 'contact_phone', 'message',
        'direction', 'status', 'external_id',
    ];

    public function config() { return $this->belongsTo(WhatsAppChatbotConfig::class, 'whatsapp_chatbot_config_id'); }
}
