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
        Schema::create('client_communication_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('communication_type', ['email', 'sms', 'phone', 'in_person', 'push_notification', 'system_generated'])->default('email');
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['sent', 'delivered', 'read', 'failed', 'pending'])->default('sent');
            $table->string('channel')->nullable(); // email, sms, phone, etc.
            $table->string('recipient')->nullable(); // email address, phone number, etc.
            $table->string('sender')->nullable(); // staff name, system, etc.
            $table->json('metadata')->nullable(); // Additional data like email headers, SMS details, etc.
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('notes')->nullable(); // Staff notes about the communication
            $table->boolean('is_important')->default(false);
            $table->boolean('requires_follow_up')->default(false);
            $table->timestamp('follow_up_date')->nullable();
            $table->string('follow_up_notes')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'created_at']);
            $table->index(['communication_type', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['staff_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_communication_history');
    }
};