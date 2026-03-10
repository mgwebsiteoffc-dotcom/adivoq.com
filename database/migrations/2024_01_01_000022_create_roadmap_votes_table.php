<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_item_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->string('session_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['roadmap_item_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_votes');
    }
};