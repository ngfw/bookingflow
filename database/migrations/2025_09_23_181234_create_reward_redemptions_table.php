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
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_point_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reward_type'); // discount, product, service, cash_back, gift_card
            $table->string('reward_name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->decimal('discount_amount', 10, 2)->nullable(); // For discount rewards
            $table->decimal('discount_percentage', 5, 2)->nullable(); // For percentage discounts
            $table->decimal('cash_value', 10, 2)->nullable(); // Cash equivalent value
            $table->string('status')->default('pending'); // pending, approved, redeemed, expired, cancelled
            $table->string('redemption_code')->unique()->nullable(); // Unique code for redemption
            $table->date('expiry_date')->nullable(); // When the reward expires
            $table->date('redeemed_date')->nullable(); // When it was actually redeemed
            $table->foreignId('redeemed_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable(); // Staff notes
            $table->json('metadata')->nullable(); // Additional data like product details, service info
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['reward_type', 'status']);
            $table->index(['expiry_date', 'status']);
            $table->index('redemption_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};