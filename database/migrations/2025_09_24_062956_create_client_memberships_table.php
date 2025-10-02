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
        Schema::create('client_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('membership_tier_id')->constrained('membership_tiers')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null for lifetime memberships
            $table->string('status')->default('active'); // active, expired, suspended, cancelled
            $table->decimal('total_spent', 10, 2)->default(0); // Total spent while in this tier
            $table->integer('total_visits')->default(0); // Total visits while in this tier
            $table->integer('total_points_earned')->default(0); // Total points earned in this tier
            $table->date('last_visit_date')->nullable();
            $table->date('next_review_date')->nullable(); // When to review for tier upgrade/downgrade
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['membership_tier_id', 'status']);
            $table->index(['status', 'end_date']);
            $table->index(['next_review_date']);
            $table->unique(['client_id', 'membership_tier_id', 'start_date'], 'client_memberships_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_memberships');
    }
};