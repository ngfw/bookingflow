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
        Schema::create('staff_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->enum('pay_period_type', ['weekly', 'bi_weekly', 'monthly'])->default('monthly');
            $table->integer('hours_worked')->default(0); // in minutes
            $table->integer('overtime_hours')->default(0); // in minutes
            $table->decimal('hourly_rate', 8, 2)->default(0.00);
            $table->decimal('overtime_rate', 8, 2)->default(0.00);
            $table->decimal('regular_pay', 10, 2)->default(0.00);
            $table->decimal('overtime_pay', 10, 2)->default(0.00);
            $table->decimal('commission_earned', 10, 2)->default(0.00);
            $table->decimal('bonus_amount', 10, 2)->default(0.00);
            $table->decimal('gross_pay', 10, 2)->default(0.00);
            $table->decimal('tax_deduction', 10, 2)->default(0.00);
            $table->decimal('social_security', 10, 2)->default(0.00);
            $table->decimal('medicare', 10, 2)->default(0.00);
            $table->decimal('health_insurance', 10, 2)->default(0.00);
            $table->decimal('retirement_contribution', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_pay', 10, 2)->default(0.00);
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid'])->default('draft');
            $table->date('pay_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('payroll_details')->nullable(); // Store detailed breakdown
            $table->timestamps();
            
            $table->unique(['staff_id', 'pay_period_start', 'pay_period_end']); // One payroll per staff per period
            $table->index(['staff_id', 'pay_period_start']);
            $table->index(['status', 'pay_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_payroll');
    }
};