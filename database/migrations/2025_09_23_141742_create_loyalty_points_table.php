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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_type'); // earned, redeemed, expired, adjusted
            $table->integer('points'); // positive for earned, negative for redeemed/expired
            $table->string('source'); // appointment, purchase, referral, bonus, manual_adjustment
            $table->string('description')->nullable();
            $table->decimal('transaction_value', 10, 2)->nullable(); // Dollar value that generated points
            $table->decimal('points_per_dollar', 5, 2)->default(1.00); // Points earned per dollar
            $table->date('expiry_date')->nullable(); // When points expire
            $table->boolean('is_expired')->default(false);
            $table->json('metadata')->nullable(); // Additional data like service details, staff info
            $table->timestamps();
            
            $table->index(['client_id', 'created_at']);
            $table->index(['transaction_type', 'created_at']);
            $table->index(['source', 'created_at']);
            $table->index(['expiry_date', 'is_expired']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};