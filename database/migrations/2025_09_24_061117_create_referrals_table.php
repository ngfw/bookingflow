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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('clients')->onDelete('cascade'); // Client who made the referral
            $table->foreignId('referred_id')->nullable()->constrained('clients')->onDelete('set null'); // Client who was referred
            $table->string('referred_email')->nullable(); // Email of referred person (if not yet registered)
            $table->string('referred_name')->nullable(); // Name of referred person
            $table->string('referred_phone')->nullable(); // Phone of referred person
            $table->string('referral_code')->unique(); // Unique referral code
            $table->string('status')->default('pending'); // pending, completed, expired, cancelled
            $table->string('referral_method')->default('code'); // code, link, manual, social_media
            $table->text('notes')->nullable(); // Additional notes
            $table->date('expiry_date')->nullable(); // When the referral expires
            $table->date('completed_date')->nullable(); // When the referral was completed
            $table->foreignId('completed_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('completed_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->decimal('referrer_reward_amount', 10, 2)->nullable(); // Reward amount for referrer
            $table->decimal('referred_reward_amount', 10, 2)->nullable(); // Reward amount for referred person
            $table->integer('referrer_points')->default(0); // Points earned by referrer
            $table->integer('referred_points')->default(0); // Points earned by referred person
            $table->boolean('referrer_reward_claimed')->default(false);
            $table->boolean('referred_reward_claimed')->default(false);
            $table->json('metadata')->nullable(); // Additional data like source, campaign, etc.
            $table->timestamps();
            
            $table->index(['referrer_id', 'status']);
            $table->index(['referred_id', 'status']);
            $table->index(['referral_code']);
            $table->index(['status', 'created_at']);
            $table->index(['expiry_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};