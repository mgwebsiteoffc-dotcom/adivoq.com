<?php
// database/migrations/0001_01_01_000005_create_waitlist_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('creator_type')->nullable();
            // youtuber, instagrammer, podcaster, blogger, multi, other
            $table->string('followers_count')->nullable();
            $table->string('source')->default('landing');
            // landing, blog, referral, social
            $table->string('status')->default('waiting');
            // waiting, invited, converted
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist');
    }
};