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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y', 'bulk_discount']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->integer('minimum_quantity')->default(1);
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->integer('buy_quantity')->nullable(); // For buy X get Y
            $table->integer('get_quantity')->nullable(); // For buy X get Y
            $table->decimal('get_discount_percentage', 5, 2)->nullable(); // For buy X get Y
            $table->json('applicable_products')->nullable(); // Product IDs
            $table->json('applicable_categories')->nullable(); // Category IDs
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_limit')->nullable(); // Max number of uses
            $table->integer('used_count')->default(0);
            $table->string('promo_code')->nullable(); // Optional promo code
            $table->boolean('requires_promo_code')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};