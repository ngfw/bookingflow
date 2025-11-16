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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'two_factor_secret')) {
                    $table->text('two_factor_secret')->nullable()->after('remember_token');
                }
                if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                    $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
                }
                if (!Schema::hasColumn('users', 'two_factor_enabled_at')) {
                    $table->timestamp('two_factor_enabled_at')->nullable()->after('two_factor_enabled');
                }
                if (!Schema::hasColumn('users', 'two_factor_disabled_at')) {
                    $table->timestamp('two_factor_disabled_at')->nullable()->after('two_factor_enabled_at');
                }
                if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                    $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_disabled_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columnsToRemove = [];

                if (Schema::hasColumn('users', 'two_factor_secret')) {
                    $columnsToRemove[] = 'two_factor_secret';
                }
                if (Schema::hasColumn('users', 'two_factor_enabled')) {
                    $columnsToRemove[] = 'two_factor_enabled';
                }
                if (Schema::hasColumn('users', 'two_factor_enabled_at')) {
                    $columnsToRemove[] = 'two_factor_enabled_at';
                }
                if (Schema::hasColumn('users', 'two_factor_disabled_at')) {
                    $columnsToRemove[] = 'two_factor_disabled_at';
                }
                if (Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                    $columnsToRemove[] = 'two_factor_recovery_codes';
                }

                if (!empty($columnsToRemove)) {
                    $table->dropColumn($columnsToRemove);
                }
            });
        }
    }
};
