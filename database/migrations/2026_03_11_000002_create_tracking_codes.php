<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('admin_users')->cascadeOnDelete();
            $table->string('service_name'); // meta_pixel, google_analytics, clarity, custom
            $table->string('tracking_id')->nullable(); // Pixel ID, GA ID, etc
            $table->string('display_name')->nullable(); // Custom name for display
            $table->text('code')->nullable(); // Full tracking code for custom scripts
            $table->json('configuration')->nullable(); // Additional settings
            $table->boolean('is_enabled')->default(true);
            $table->text('note')->nullable(); // Admin notes
            $table->timestamps();

            $table->index('service_name');
            $table->index('is_enabled');
            $table->index('created_at');
        });

        Schema::create('tracked_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('event_name'); // page_view, purchase, add_to_cart, etc
            $table->string('event_category')->nullable();
            $table->json('event_data')->nullable(); // Custom data/parameters
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->uuid('session_id')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('event_name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_events');
        Schema::dropIfExists('tracking_codes');
    }
};
