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
        Schema::create('promotional_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Campaign name
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->text('description')->nullable(); // Campaign description
            $table->string('type'); // discount, bonus_points, free_service, package_deal, seasonal, referral_bonus
            $table->string('status')->default('draft'); // draft, active, paused, completed, cancelled
            $table->date('start_date'); // Campaign start date
            $table->date('end_date')->nullable(); // Campaign end date
            $table->time('start_time')->nullable(); // Daily start time
            $table->time('end_time')->nullable(); // Daily end time
            $table->json('days_of_week')->nullable(); // Days of week to run (1-7)
            $table->json('target_audience')->nullable(); // Target audience criteria
            $table->json('discount_settings')->nullable(); // Discount configuration
            $table->json('bonus_points_settings')->nullable(); // Bonus points configuration
            $table->json('free_service_settings')->nullable(); // Free service configuration
            $table->json('package_deal_settings')->nullable(); // Package deal configuration
            $table->json('seasonal_settings')->nullable(); // Seasonal campaign settings
            $table->json('referral_bonus_settings')->nullable(); // Referral bonus settings
            $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Minimum purchase amount
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Maximum discount amount
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_client')->nullable(); // Usage limit per client
            $table->integer('current_usage')->default(0); // Current usage count
            $table->boolean('is_automatic')->default(false); // Automatic application
            $table->boolean('requires_code')->default(false); // Requires promo code
            $table->string('promo_code')->nullable(); // Promotional code
            $table->json('channels')->nullable(); // Marketing channels (email, sms, push, social)
            $table->json('creative_assets')->nullable(); // Images, banners, etc.
            $table->json('tracking_settings')->nullable(); // Analytics and tracking
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['type', 'status']);
            $table->index(['promo_code']);
            $table->index(['is_automatic', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotional_campaigns');
    }
};