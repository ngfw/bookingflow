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
        Schema::create('salon_settings', function (Blueprint $table) {
            $table->id();
            $table->string('salon_name');
            $table->text('salon_description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color', 7)->default('#ec4899'); // Pink
            $table->string('secondary_color', 7)->default('#8b5cf6'); // Purple
            $table->string('accent_color', 7)->default('#f59e0b'); // Amber
            $table->string('font_family')->default('Inter');
            $table->json('contact_info')->nullable(); // phone, email, address, hours
            $table->json('social_links')->nullable(); // facebook, instagram, twitter, etc.
            $table->json('seo_settings')->nullable(); // meta title, description, keywords
            $table->json('homepage_settings')->nullable(); // hero content, featured services, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salon_settings');
    }
};
