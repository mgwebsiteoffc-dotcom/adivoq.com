<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('name')->nullable();
            $table->enum('type', ['pixel', 'facebook', 'google_ads', 'hotjar', 'custom'])->default('pixel');
            $table->text('config')->nullable(); // JSON for storing extra config
            $table->boolean('is_active')->default(true);
            $table->integer('monthly_events')->default(0);
            $table->integer('monthly_limit')->default(0);
            $table->timestamps();
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracking_key_id')->constrained('tracking_keys')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('event_name');
            $table->json('event_data')->nullable();
            $table->string('source_url')->nullable();
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
            
            $table->index(['tracking_key_id', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('whatsapp_chatbot_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number')->unique();
            $table->string('webhook_token')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('auto_replies')->nullable(); // Keywords and responses
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index('phone_number');
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_chatbot_config_id')->constrained('whatsapp_chatbot_configs')->cascadeOnDelete();
            $table->string('contact_phone');
            $table->longText('message');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed']);
            $table->string('external_id')->nullable();
            $table->timestamps();
            
            $table->index(['contact_phone', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_chatbot_configs');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('tracking_keys');
    }
};
