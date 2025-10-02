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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'digital_wallet', 'check', 'gift_card'])->default('cash');
            $table->string('transaction_id')->nullable(); // For digital payments
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('reference_number')->nullable();
            $table->decimal('processing_fee', 8, 2)->default(0);
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null'); // Staff who processed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
