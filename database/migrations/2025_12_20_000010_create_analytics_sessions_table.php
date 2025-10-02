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
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('device_info')->nullable();
            $table->json('location_info')->nullable();
            $table->string('referrer')->nullable();
            $table->string('landing_page')->nullable();
            $table->integer('page_views')->default(0);
            $table->integer('duration')->default(0); // in seconds
            $table->boolean('is_bounce')->default(false);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->index(['started_at']);
            $table->index(['user_id', 'started_at']);
            $table->index(['landing_page', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
