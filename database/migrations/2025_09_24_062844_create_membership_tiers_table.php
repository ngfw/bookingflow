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
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Platinum
            $table->string('slug')->unique(); // bronze, silver, gold, platinum
            $table->text('description')->nullable();
            $table->string('color')->default('#6B7280'); // Hex color for UI
            $table->string('icon')->nullable(); // Icon class or path
            $table->integer('min_points')->default(0); // Minimum points required
            $table->integer('max_points')->nullable(); // Maximum points for this tier
            $table->decimal('min_spent', 10, 2)->default(0); // Minimum amount spent
            $table->decimal('max_spent', 10, 2)->nullable(); // Maximum amount spent for this tier
            $table->integer('min_visits')->default(0); // Minimum visits required
            $table->integer('max_visits')->nullable(); // Maximum visits for this tier
            $table->decimal('discount_percentage', 5, 2)->default(0); // Discount percentage
            $table->decimal('discount_amount', 10, 2)->default(0); // Fixed discount amount
            $table->integer('bonus_points_multiplier')->default(1); // Points multiplier (e.g., 1.5x)
            $table->boolean('free_shipping')->default(false);
            $table->boolean('priority_booking')->default(false);
            $table->boolean('exclusive_services')->default(false);
            $table->boolean('birthday_bonus')->default(false);
            $table->boolean('anniversary_bonus')->default(false);
            $table->json('benefits')->nullable(); // Additional benefits
            $table->json('restrictions')->nullable(); // Service restrictions
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index(['min_points', 'max_points']);
            $table->index(['min_spent', 'max_spent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};