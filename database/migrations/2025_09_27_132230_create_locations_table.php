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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., 'SF001', 'LA002'
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country')->default('US');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('business_hours')->nullable(); // Store business hours as JSON
            $table->json('amenities')->nullable(); // Store amenities as JSON
            $table->string('timezone')->default('America/Los_Angeles');
            $table->decimal('tax_rate', 5, 4)->default(0.08); // Default 8% tax rate
            $table->string('currency')->default('USD');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_headquarters')->default(false);
            $table->integer('max_staff')->nullable(); // Maximum staff capacity
            $table->integer('max_clients_per_day')->nullable(); // Maximum daily client capacity
            $table->json('settings')->nullable(); // Additional location-specific settings
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};