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
        Schema::create('staff_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('performance_date');
            $table->integer('appointments_completed')->default(0);
            $table->integer('appointments_cancelled')->default(0);
            $table->integer('appointments_no_show')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0.00);
            $table->decimal('commission_earned', 10, 2)->default(0.00);
            $table->integer('hours_worked')->default(0); // in minutes
            $table->integer('overtime_hours')->default(0); // in minutes
            $table->decimal('client_satisfaction_rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->integer('products_used')->default(0);
            $table->decimal('product_cost', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->json('performance_metrics')->nullable(); // Store additional metrics
            $table->timestamps();
            
            $table->unique(['staff_id', 'performance_date']); // One record per staff per day
            $table->index(['staff_id', 'performance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_performance');
    }
};