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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('appointment_number')->unique()->nullable();
            $table->dateTime('appointment_date');
            $table->dateTime('end_time')->nullable();
            $table->integer('duration')->nullable(); // Duration in minutes
            $table->enum('status', ['pending', 'confirmed', 'scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('booking_source', ['online', 'phone', 'walk_in', 'staff', 'admin'])->default('online');
            $table->decimal('price', 8, 2)->nullable(); // Base price
            $table->decimal('service_price', 8, 2)->nullable(); // Price at time of booking
            $table->decimal('deposit_paid', 8, 2)->default(0);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('client_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_pattern')->nullable(); // weekly, monthly, etc.
            $table->date('recurring_end_date')->nullable();
            $table->integer('reminder_hours')->nullable(); // Hours before appointment
            $table->boolean('reminder_sent')->default(false);
            $table->dateTime('reminder_sent_at')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
