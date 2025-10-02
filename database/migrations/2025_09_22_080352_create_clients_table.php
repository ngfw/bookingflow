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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('preferences')->nullable(); // JSON field for client preferences
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->date('last_visit')->nullable();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('visit_count')->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->enum('preferred_contact', ['email', 'phone', 'sms'])->default('email');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
