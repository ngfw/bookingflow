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
        Schema::create('client_special_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_id')->constrained('birthday_anniversary_specials')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->string('event_type'); // birthday, anniversary
            $table->date('event_date'); // The actual birthday/anniversary date
            $table->date('special_date'); // When the special was used
            $table->decimal('original_amount', 10, 2)->nullable(); // Original amount before discount
            $table->decimal('discount_amount', 10, 2)->nullable(); // Discount amount applied
            $table->decimal('final_amount', 10, 2)->nullable(); // Final amount after discount
            $table->integer('bonus_points_earned')->default(0); // Bonus points earned
            $table->string('status')->default('used'); // used, expired, cancelled
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['special_id', 'client_id']);
            $table->index(['client_id', 'event_date']);
            $table->index(['event_type', 'event_date']);
            $table->index(['status', 'special_date']);
            $table->unique(['special_id', 'client_id', 'event_date', 'event_type'], 'unique_client_special_per_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_special_usage');
    }
};