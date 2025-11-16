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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('duration_minutes');
            $table->integer('buffer_time_minutes')->default(15); // Time between appointments
            $table->boolean('requires_deposit')->default(false);
            $table->decimal('deposit_amount', 8, 2)->nullable();
            $table->json('required_products')->nullable(); // Product IDs used in service
            $table->boolean('is_package')->default(false);
            $table->json('package_services')->nullable(); // Service IDs if it's a package
            $table->boolean('online_booking_enabled')->default(true);
            $table->integer('max_advance_booking_days')->default(30);
            $table->text('preparation_instructions')->nullable();
            $table->text('aftercare_instructions')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->boolean('requires_consultation')->default(false);
            $table->integer('duration')->nullable(); // Alias for duration_minutes
            $table->text('price_change_reason')->nullable();
            $table->text('duration_change_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
