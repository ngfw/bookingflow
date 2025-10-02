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
        Schema::create('retention_insights', function (Blueprint $table) {
            $table->id();
            $table->string('insight_type'); // client_insight, cohort_analysis, trend_analysis, prediction, recommendation
            $table->string('title'); // Insight title
            $table->text('description'); // Detailed description
            $table->string('category'); // retention, engagement, loyalty, satisfaction, revenue
            $table->string('priority'); // low, medium, high, critical
            $table->string('status')->default('active'); // active, dismissed, implemented, expired
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->json('data')->nullable(); // Insight data and metrics
            $table->json('recommendations')->nullable(); // Actionable recommendations
            $table->json('targets')->nullable(); // Target metrics or goals
            $table->date('insight_date'); // Date when insight was generated
            $table->date('expiry_date')->nullable(); // When insight expires
            $table->boolean('is_automated')->default(true); // Generated automatically or manually
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['insight_type', 'status']);
            $table->index(['category', 'priority']);
            $table->index(['client_id', 'insight_date']);
            $table->index(['status', 'expiry_date']);
            $table->index(['is_automated', 'insight_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_insights');
    }
};