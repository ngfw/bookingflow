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
        Schema::create('campaign_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('promotional_campaigns')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->string('usage_type')->default('appointment'); // appointment, purchase, referral, manual
            $table->decimal('original_amount', 10, 2)->nullable(); // Original amount before discount
            $table->decimal('discount_amount', 10, 2)->nullable(); // Discount amount applied
            $table->decimal('final_amount', 10, 2)->nullable(); // Final amount after discount
            $table->integer('bonus_points_earned')->default(0); // Bonus points earned
            $table->string('promo_code_used')->nullable(); // Promo code used
            $table->string('channel')->nullable(); // Channel through which campaign was used
            $table->string('status')->default('completed'); // completed, cancelled, refunded
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['campaign_id', 'client_id']);
            $table->index(['campaign_id', 'usage_type']);
            $table->index(['client_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_usage');
    }
};