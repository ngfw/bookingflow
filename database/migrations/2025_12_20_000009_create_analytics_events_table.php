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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // page_view, click, form_submit, etc.
            $table->string('event_name'); // specific event name
            $table->json('event_data')->nullable(); // event-specific data
            $table->string('page_url');
            $table->string('page_title')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('referrer')->nullable();
            $table->string('session_id')->nullable();
            $table->string('user_id')->nullable(); // if user is logged in
            $table->json('device_info')->nullable(); // device, browser, os info
            $table->json('location_info')->nullable(); // country, city, etc.
            $table->timestamp('created_at');
            
            $table->index(['event_type', 'created_at']);
            $table->index(['page_url', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
