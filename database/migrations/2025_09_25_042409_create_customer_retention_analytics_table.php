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
        Schema::create('customer_retention_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->date('analysis_date'); // Date of analysis
            $table->string('period_type'); // daily, weekly, monthly, quarterly, yearly
            $table->date('period_start'); // Start of analysis period
            $table->date('period_end'); // End of analysis period
            $table->integer('total_appointments')->default(0); // Total appointments in period
            $table->integer('completed_appointments')->default(0); // Completed appointments
            $table->integer('cancelled_appointments')->default(0); // Cancelled appointments
            $table->integer('no_show_appointments')->default(0); // No-show appointments
            $table->decimal('total_revenue', 10, 2)->default(0); // Total revenue in period
            $table->decimal('average_appointment_value', 10, 2)->default(0); // Average appointment value
            $table->integer('days_since_last_visit')->nullable(); // Days since last visit
            $table->integer('days_since_first_visit')->nullable(); // Days since first visit
            $table->integer('visit_frequency')->default(0); // Visits per month
            $table->decimal('retention_score', 5, 2)->default(0); // Retention score (0-100)
            $table->string('retention_status'); // active, at_risk, inactive, churned
            $table->json('engagement_metrics')->nullable(); // Engagement metrics
            $table->json('loyalty_metrics')->nullable(); // Loyalty program metrics
            $table->json('satisfaction_metrics')->nullable(); // Satisfaction metrics
            $table->json('predictive_metrics')->nullable(); // Predictive analytics
            $table->json('recommendations')->nullable(); // AI-generated recommendations
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['client_id', 'analysis_date'], 'retention_client_date_index');
            $table->index(['period_type', 'period_start', 'period_end'], 'retention_period_index');
            $table->index(['retention_status', 'analysis_date'], 'retention_status_date_index');
            $table->index(['retention_score', 'analysis_date'], 'retention_score_date_index');
            $table->unique(['client_id', 'analysis_date', 'period_type'], 'unique_client_period_analysis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_retention_analytics');
    }
};