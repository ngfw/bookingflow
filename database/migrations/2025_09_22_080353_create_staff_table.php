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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('position');
            $table->string('specializations')->nullable(); // JSON field for specializations
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0); // Percentage
            $table->date('hire_date');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract'])->default('full-time');
            $table->time('default_start_time')->nullable();
            $table->time('default_end_time')->nullable();
            $table->json('working_days')->nullable(); // [1,2,3,4,5] for Mon-Fri
            $table->boolean('can_book_online')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('skills')->nullable(); // JSON array
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
