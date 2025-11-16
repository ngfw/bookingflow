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
        // Add franchise_id to locations table
        if (Schema::hasTable('locations') && !Schema::hasColumn('locations', 'franchise_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->foreignId('franchise_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('franchise_id');
            });
        }

        // Add franchise_id to users table for franchise owners/managers
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'franchise_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('franchise_id')->nullable()->constrained()->onDelete('set null');
                $table->index('franchise_id');
            });
        }

        // Create franchise payments table for tracking franchise fees
        if (!Schema::hasTable('franchise_payments')) {
            Schema::create('franchise_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained()->onDelete('cascade');
                $table->string('payment_type'); // royalty, marketing_fee, technology_fee, initial_fee, penalty, other
                $table->decimal('amount', 10, 2);
                $table->date('due_date');
                $table->date('paid_date')->nullable();
                $table->string('status')->default('pending'); // pending, paid, overdue, waived
                $table->string('payment_method')->nullable();
                $table->string('transaction_reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['franchise_id', 'status']);
                $table->index(['due_date', 'status']);
            });
        }

        // Create franchise performance metrics table
        if (!Schema::hasTable('franchise_metrics')) {
            Schema::create('franchise_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained()->onDelete('cascade');
                $table->date('metric_date');
                $table->string('metric_type'); // sales, appointments, customer_satisfaction, compliance, training
                $table->decimal('metric_value', 15, 4);
                $table->string('metric_unit')->nullable(); // dollars, count, percentage, rating
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['franchise_id', 'metric_date', 'metric_type']);
                $table->unique(['franchise_id', 'metric_date', 'metric_type']);
            });
        }

        // Create franchise communications table
        if (!Schema::hasTable('franchise_communications')) {
            Schema::create('franchise_communications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained()->onDelete('cascade');
                $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('communication_type'); // email, phone, meeting, training, support
                $table->string('subject');
                $table->text('message');
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->string('status')->default('sent'); // sent, read, responded, closed
                $table->timestamp('read_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->json('attachments')->nullable();
                $table->timestamps();

                $table->index(['franchise_id', 'status']);
                $table->index(['communication_type', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_communications');
        Schema::dropIfExists('franchise_metrics');
        Schema::dropIfExists('franchise_payments');

        if (Schema::hasTable('locations') && Schema::hasColumn('locations', 'franchise_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropForeign(['franchise_id']);
                $table->dropColumn('franchise_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'franchise_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['franchise_id']);
                $table->dropColumn('franchise_id');
            });
        }
    }
};