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
        // Add location_id to staff table
        if (Schema::hasTable('staff') && !Schema::hasColumn('staff', 'location_id')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to appointments table
        if (Schema::hasTable('appointments') && !Schema::hasColumn('appointments', 'location_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to invoices table
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'location_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to products table
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'location_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to services table
        if (Schema::hasTable('services') && !Schema::hasColumn('services', 'location_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to schedules table
        if (Schema::hasTable('schedules') && !Schema::hasColumn('schedules', 'location_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to payments table
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'location_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
                $table->index('location_id');
            });
        }

        // Add location_id to users table for staff assignment
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'primary_location_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('primary_location_id')->nullable()->constrained('locations')->onDelete('set null');
                $table->index('primary_location_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'location_id')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'location_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'location_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'location_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('services') && Schema::hasColumn('services', 'location_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('schedules') && Schema::hasColumn('schedules', 'location_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'location_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'primary_location_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['primary_location_id']);
                $table->dropColumn('primary_location_id');
            });
        }
    }
};