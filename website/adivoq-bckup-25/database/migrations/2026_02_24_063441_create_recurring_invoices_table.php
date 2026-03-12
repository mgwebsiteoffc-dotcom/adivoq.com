<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('recurring_invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
        $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();

        $table->string('title');
        $table->decimal('amount', 12, 2);

        $table->string('frequency'); // monthly, quarterly
        $table->date('start_date');
        $table->date('next_run_date');

        $table->boolean('active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
