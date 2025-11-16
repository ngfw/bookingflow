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
        if (Schema::hasTable('waitlist') && !Schema::hasTable('waitlists')) {
            Schema::rename('waitlist', 'waitlists');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('waitlists') && !Schema::hasTable('waitlist')) {
            Schema::rename('waitlists', 'waitlist');
        }
    }
};
