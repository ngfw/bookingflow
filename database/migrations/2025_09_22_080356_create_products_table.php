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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('selling_price', 8, 2);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->string('unit', 20)->default('piece'); // piece, ml, gram, etc.
            $table->string('supplier')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('storage_location')->nullable();
            $table->boolean('is_for_sale')->default(true); // Can be sold to clients
            $table->boolean('is_for_service')->default(false); // Used in services
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('usage_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
