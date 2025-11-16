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
        if (Schema::hasTable('salon_settings')) {
            Schema::table('salon_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('salon_settings', 'booking_settings')) {
                    $table->json('booking_settings')->nullable()->after('homepage_settings');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('salon_settings')) {
            Schema::table('salon_settings', function (Blueprint $table) {
                if (Schema::hasColumn('salon_settings', 'booking_settings')) {
                    $table->dropColumn('booking_settings');
                }
            });
        }
    }
};