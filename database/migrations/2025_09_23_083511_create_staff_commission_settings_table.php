<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_commission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('commission_type', ['percentage', 'fixed', 'tiered'])->default('percentage');
            $table->decimal('commission_rate', 5, 2)->default(0.00); // 0.00 to 100.00
            $table->decimal('fixed_amount', 10, 2)->nullable(); // For fixed commission
            $table->json('tiered_rates')->nullable(); // For tiered commission structure
            $table->decimal('minimum_threshold', 10, 2)->default(0.00); // Minimum amount to earn commission
            $table->decimal('maximum_cap', 10, 2)->nullable(); // Maximum commission per period
            $table->enum('calculation_basis', ['revenue', 'profit', 'appointments'])->default('revenue');
            $table->enum('payment_frequency', ['daily', 'weekly', 'bi_weekly', 'monthly'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->date('effective_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['staff_id', 'service_id']); // One setting per staff per service
            $table->index(['staff_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_commission_settings');
    }
};