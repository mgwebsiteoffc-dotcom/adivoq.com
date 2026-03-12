<?php
// database/migrations/0001_01_01_000001_create_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('plan')->default('free');
            $table->string('status')->default('active');
            // active, suspended, trial, cancelled
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            /*
            settings JSON structure:
            {
                "currency": "INR",
                "timezone": "Asia/Kolkata",
                "date_format": "d/m/Y",
                "financial_year_start": "04"  // April
            }
            */
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};