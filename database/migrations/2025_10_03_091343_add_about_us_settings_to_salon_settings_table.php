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
        Schema::table('salon_settings', function (Blueprint $table) {
            $table->text('about_us_title')->nullable();
            $table->longText('about_us_content')->nullable();
            $table->text('about_us_mission')->nullable();
            $table->text('about_us_vision')->nullable();
            $table->boolean('show_team_on_about')->default(true);
            $table->string('about_us_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salon_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_us_title',
                'about_us_content',
                'about_us_mission',
                'about_us_vision',
                'show_team_on_about',
                'about_us_image'
            ]);
        });
    }
};
