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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('retail_price', 10, 2)->nullable()->after('selling_price');
            $table->integer('stock_quantity')->default(0)->after('current_stock');
            $table->string('type')->default('service')->after('is_for_service'); // 'service' or 'retail'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['retail_price', 'stock_quantity', 'type']);
        });
    }
};