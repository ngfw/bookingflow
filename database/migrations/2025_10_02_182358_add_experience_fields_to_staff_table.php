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
        Schema::table('staff', function (Blueprint $table) {
            $table->integer('experience_years')->nullable()->after('bio');
            $table->text('certifications')->nullable()->after('experience_years');
            $table->text('education')->nullable()->after('certifications');
            $table->text('achievements')->nullable()->after('education');
            $table->json('social_media')->nullable()->after('achievements');
            $table->text('languages')->nullable()->after('social_media');
            $table->text('hobbies')->nullable()->after('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'experience_years',
                'certifications',
                'education',
                'achievements',
                'social_media',
                'languages',
                'hobbies'
            ]);
        });
    }
};