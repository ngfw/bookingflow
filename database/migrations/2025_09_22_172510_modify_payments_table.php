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
        Schema::table('payments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('payments', 'payment_number')) {
                $table->string('payment_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('payments', 'processed_by')) {
                $table->foreignId('processed_by')->after('client_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('payments', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('reference_number');
            }
            if (!Schema::hasColumn('payments', 'payment_details')) {
                $table->json('payment_details')->nullable()->after('transaction_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_number', 'processed_by', 'reference_number', 'transaction_id', 'payment_details']);
        });
    }
};