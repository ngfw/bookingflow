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
        Schema::create('birthday_anniversary_specials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Special name
            $table->string('type'); // birthday, anniversary, both
            $table->text('description')->nullable(); // Description of the special
            $table->string('status')->default('active'); // active, inactive, expired
            $table->date('start_date')->nullable(); // When the special becomes available
            $table->date('end_date')->nullable(); // When the special expires
            $table->integer('days_before')->default(7); // Days before the event to notify
            $table->integer('days_after')->default(7); // Days after the event the special is valid
            $table->json('discount_settings')->nullable(); // Discount configuration
            $table->json('bonus_points_settings')->nullable(); // Bonus points configuration
            $table->json('free_service_settings')->nullable(); // Free service configuration
            $table->json('gift_settings')->nullable(); // Gift configuration
            $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Minimum purchase amount
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Maximum discount amount
            $table->integer('usage_limit_per_client')->default(1); // Usage limit per client per year
            $table->boolean('requires_appointment')->default(true); // Must book appointment to use
            $table->boolean('auto_apply')->default(false); // Automatically apply to eligible clients
            $table->json('notification_settings')->nullable(); // Notification configuration
            $table->json('target_criteria')->nullable(); // Target client criteria
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'auto_apply']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_anniversary_specials');
    }
};