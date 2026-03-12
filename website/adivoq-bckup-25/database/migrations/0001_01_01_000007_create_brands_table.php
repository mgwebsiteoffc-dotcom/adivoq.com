<?php
// database/migrations/0001_01_01_000007_create_brands_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->json('address')->nullable();
            /*
            {
                "line1": "123 MG Road",
                "line2": "Suite 400",
                "city": "Mumbai",
                "state": "Maharashtra",
                "state_code": "27",
                "country": "India",
                "zip": "400001"
            }
            */
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->string('payment_terms')->default('net_30');
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            // active, inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};