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
        Schema::table('invoices', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('invoices', 'created_by')) {
                $table->foreignId('created_by')->after('appointment_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('invoices', 'paid_date')) {
                $table->date('paid_date')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('invoices', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'paid_date', 'terms_conditions']);
        });
    }
};