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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_type'); // appointment_reminder, appointment_confirmation, appointment_cancellation, promotion, system_update, etc.
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('phone_enabled')->default(false);
            $table->json('timing_preferences')->nullable(); // When to send notifications (e.g., 24h, 2h, 1h before)
            $table->json('frequency_preferences')->nullable(); // How often to send (daily, weekly, etc.)
            $table->json('content_preferences')->nullable(); // What content to include
            $table->string('preferred_language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'notification_type']); // One preference per user per type
            $table->index(['user_id', 'is_active']);
            $table->index(['notification_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};