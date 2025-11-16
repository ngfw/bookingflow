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
                if (!Schema::hasColumn('salon_settings', 'about_us_title')) {
                    $table->text('about_us_title')->nullable();
                }
                if (!Schema::hasColumn('salon_settings', 'about_us_content')) {
                    $table->longText('about_us_content')->nullable();
                }
                if (!Schema::hasColumn('salon_settings', 'about_us_mission')) {
                    $table->text('about_us_mission')->nullable();
                }
                if (!Schema::hasColumn('salon_settings', 'about_us_vision')) {
                    $table->text('about_us_vision')->nullable();
                }
                if (!Schema::hasColumn('salon_settings', 'show_team_on_about')) {
                    $table->boolean('show_team_on_about')->default(true);
                }
                if (!Schema::hasColumn('salon_settings', 'about_us_image')) {
                    $table->string('about_us_image')->nullable();
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
                $columnsToRemove = [];

                if (Schema::hasColumn('salon_settings', 'about_us_title')) {
                    $columnsToRemove[] = 'about_us_title';
                }
                if (Schema::hasColumn('salon_settings', 'about_us_content')) {
                    $columnsToRemove[] = 'about_us_content';
                }
                if (Schema::hasColumn('salon_settings', 'about_us_mission')) {
                    $columnsToRemove[] = 'about_us_mission';
                }
                if (Schema::hasColumn('salon_settings', 'about_us_vision')) {
                    $columnsToRemove[] = 'about_us_vision';
                }
                if (Schema::hasColumn('salon_settings', 'show_team_on_about')) {
                    $columnsToRemove[] = 'show_team_on_about';
                }
                if (Schema::hasColumn('salon_settings', 'about_us_image')) {
                    $columnsToRemove[] = 'about_us_image';
                }

                if (!empty($columnsToRemove)) {
                    $table->dropColumn($columnsToRemove);
                }
            });
        }
    }
};
