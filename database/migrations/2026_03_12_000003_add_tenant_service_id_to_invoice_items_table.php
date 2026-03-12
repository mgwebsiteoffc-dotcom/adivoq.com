<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('tenant_service_id')->nullable()->after('hsn_sac_code')->constrained('tenant_services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['tenant_service_id']);
            $table->dropColumn('tenant_service_id');
        });
    }
};
