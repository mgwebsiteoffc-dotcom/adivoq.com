<?php
// database/migrations/0001_01_01_000004_create_roadmap_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('feature');
            // feature, improvement, integration, bug_fix
            $table->string('status')->default('planned');
            // planned, in_progress, completed, cancelled
            $table->string('priority')->default('medium');
            // low, medium, high, critical
            $table->string('target_quarter')->nullable();
            $table->unsignedInteger('votes_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_items');
    }
};