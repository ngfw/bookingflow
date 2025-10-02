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
        Schema::create('franchises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('franchise_code')->unique(); // e.g., 'FR001', 'FR002'
            $table->text('description')->nullable();
            $table->string('franchise_type')->default('owned'); // owned, franchisee, corporate
            $table->string('status')->default('active'); // active, inactive, pending, suspended
            
            // Franchise Owner Information
            $table->string('owner_name');
            $table->string('owner_email');
            $table->string('owner_phone');
            $table->text('owner_address');
            $table->string('owner_city');
            $table->string('owner_state');
            $table->string('owner_postal_code');
            $table->string('owner_country')->default('US');
            
            // Franchise Agreement Details
            $table->date('agreement_start_date');
            $table->date('agreement_end_date')->nullable();
            $table->decimal('initial_franchise_fee', 10, 2)->default(0);
            $table->decimal('royalty_rate', 5, 4)->default(0.05); // 5% default royalty
            $table->decimal('marketing_fee_rate', 5, 4)->default(0.02); // 2% default marketing fee
            $table->decimal('technology_fee_rate', 5, 4)->default(0.01); // 1% default tech fee
            $table->string('payment_frequency')->default('monthly'); // monthly, quarterly, annually
            $table->date('next_payment_due')->nullable();
            
            // Territory and Location
            $table->json('territory_boundaries')->nullable(); // Store territory coordinates
            $table->string('territory_description')->nullable();
            $table->integer('max_locations_allowed')->default(1);
            $table->integer('current_locations_count')->default(0);
            
            // Performance Metrics
            $table->decimal('monthly_sales_target', 10, 2)->nullable();
            $table->decimal('yearly_sales_target', 12, 2)->nullable();
            $table->decimal('current_month_sales', 10, 2)->default(0);
            $table->decimal('current_year_sales', 12, 2)->default(0);
            $table->integer('current_month_appointments')->default(0);
            $table->integer('current_year_appointments')->default(0);
            
            // Compliance and Training
            $table->json('required_training')->nullable(); // Store required training modules
            $table->json('completed_training')->nullable(); // Store completed training
            $table->date('last_compliance_check')->nullable();
            $table->date('next_compliance_check')->nullable();
            $table->text('compliance_notes')->nullable();
            
            // Support and Communication
            $table->string('assigned_manager')->nullable(); // Corporate manager assigned
            $table->string('support_level')->default('standard'); // standard, premium, enterprise
            $table->json('communication_preferences')->nullable(); // Email, phone, SMS preferences
            $table->text('support_notes')->nullable();
            
            // Financial Information
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->string('payment_method')->nullable(); // credit_card, bank_transfer, check
            $table->json('banking_info')->nullable(); // Encrypted banking information
            $table->string('tax_id')->nullable();
            
            // Marketing and Branding
            $table->json('approved_marketing_materials')->nullable();
            $table->json('brand_guidelines_compliance')->nullable();
            $table->boolean('local_marketing_approved')->default(false);
            $table->decimal('local_marketing_budget', 10, 2)->default(0);
            
            // Operational Settings
            $table->json('operational_standards')->nullable();
            $table->json('quality_metrics')->nullable();
            $table->decimal('customer_satisfaction_target', 5, 2)->default(4.5); // 1-5 scale
            $table->decimal('current_satisfaction_score', 5, 2)->default(0);
            
            // Audit and History
            $table->json('audit_history')->nullable();
            $table->json('performance_history')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchises');
    }
};