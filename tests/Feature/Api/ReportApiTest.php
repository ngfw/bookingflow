<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_dashboard_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/dashboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_appointments',
                        'today_appointments',
                        'week_appointments',
                        'month_appointments',
                        'total_revenue',
                        'today_revenue',
                        'week_revenue',
                        'month_revenue',
                        'total_clients',
                        'new_clients_this_month',
                        'total_staff',
                        'active_staff',
                        'total_services',
                        'active_services',
                        'total_locations',
                        'active_locations',
                        'total_inventory_items',
                        'low_stock_items',
                        'expiring_soon_items',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_appointment_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'appointments_by_status',
                        'appointments_by_month',
                        'appointments_by_staff',
                        'appointments_by_service',
                        'appointments_by_location',
                        'cancellation_rate',
                        'no_show_rate',
                        'average_appointment_duration',
                        'peak_hours',
                        'peak_days',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_revenue_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/revenue');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_revenue',
                        'revenue_by_month',
                        'revenue_by_staff',
                        'revenue_by_service',
                        'revenue_by_location',
                        'average_revenue_per_appointment',
                        'revenue_growth_rate',
                        'top_services_by_revenue',
                        'revenue_by_payment_method',
                        'revenue_by_client_type',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_client_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/clients');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_clients',
                        'new_clients_by_month',
                        'clients_by_city',
                        'clients_by_state',
                        'clients_by_age_group',
                        'clients_by_gender',
                        'average_appointments_per_client',
                        'client_retention_rate',
                        'top_clients_by_revenue',
                        'client_satisfaction_rating',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_staff_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/staff');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_staff',
                        'staff_by_position',
                        'staff_by_location',
                        'staff_performance',
                        'staff_commission_earned',
                        'staff_utilization_rate',
                        'staff_satisfaction_rating',
                        'top_performing_staff',
                        'staff_turnover_rate',
                        'staff_training_completion',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_service_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_services',
                        'services_by_category',
                        'services_by_location',
                        'most_popular_services',
                        'least_popular_services',
                        'service_revenue',
                        'service_utilization_rate',
                        'average_service_duration',
                        'service_satisfaction_rating',
                        'service_profit_margin',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_location_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/locations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_locations',
                        'locations_by_city',
                        'locations_by_state',
                        'location_revenue',
                        'location_utilization_rate',
                        'location_capacity_utilization',
                        'location_satisfaction_rating',
                        'top_performing_locations',
                        'location_expenses',
                        'location_profit_margin',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_inventory_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/inventory');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_inventory_items',
                        'inventory_by_category',
                        'inventory_by_location',
                        'low_stock_items',
                        'expiring_soon_items',
                        'inventory_value',
                        'inventory_turnover_rate',
                        'inventory_movements',
                        'inventory_costs',
                        'inventory_profit_margin',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_financial_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/financial');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_revenue',
                        'total_expenses',
                        'net_profit',
                        'profit_margin',
                        'revenue_by_month',
                        'expenses_by_month',
                        'profit_by_month',
                        'revenue_by_source',
                        'expenses_by_category',
                        'cash_flow',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_marketing_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/marketing');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'total_campaigns',
                        'campaign_performance',
                        'email_marketing_stats',
                        'sms_marketing_stats',
                        'social_media_stats',
                        'referral_stats',
                        'loyalty_program_stats',
                        'promotion_effectiveness',
                        'customer_acquisition_cost',
                        'customer_lifetime_value',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_operational_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/operational');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'appointment_utilization',
                        'staff_utilization',
                        'location_utilization',
                        'service_utilization',
                        'equipment_utilization',
                        'maintenance_schedules',
                        'supply_usage',
                        'waste_management',
                        'energy_consumption',
                        'operational_efficiency',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_custom_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/custom');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'reports' => [
                        'custom_metrics',
                        'kpi_dashboard',
                        'benchmarking',
                        'trend_analysis',
                        'forecasting',
                        'comparative_analysis',
                        'performance_indicators',
                        'business_intelligence',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_export_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/export', [
            'report_type' => 'appointments',
            'format' => 'csv',
            'date_range' => 'last_month',
        ]);

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_schedule_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $scheduleData = [
            'report_type' => 'revenue',
            'frequency' => 'weekly',
            'email_recipients' => ['admin@salon.com'],
            'format' => 'pdf',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/reports/schedule', $scheduleData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'schedule' => [
                        'id',
                        'report_type',
                        'frequency',
                        'email_recipients',
                        'format',
                        'next_run',
                        'is_active',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_scheduled_reports_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/scheduled');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'schedules' => [
                        '*' => [
                            'id',
                            'report_type',
                            'frequency',
                            'email_recipients',
                            'format',
                            'next_run',
                            'is_active',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_scheduled_report_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        // First create a scheduled report
        $scheduleData = [
            'report_type' => 'revenue',
            'frequency' => 'weekly',
            'email_recipients' => ['admin@salon.com'],
            'format' => 'pdf',
        ];

        $createResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/reports/schedule', $scheduleData);

        $scheduleId = $createResponse->json('schedule.id');

        // Update the scheduled report
        $updateData = [
            'frequency' => 'monthly',
            'email_recipients' => ['admin@salon.com', 'manager@salon.com'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/reports/schedule/' . $scheduleId, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'schedule' => [
                        'id' => $scheduleId,
                        'frequency' => 'monthly',
                        'email_recipients' => ['admin@salon.com', 'manager@salon.com'],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_delete_scheduled_report_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        // First create a scheduled report
        $scheduleData = [
            'report_type' => 'revenue',
            'frequency' => 'weekly',
            'email_recipients' => ['admin@salon.com'],
            'format' => 'pdf',
        ];

        $createResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/reports/schedule', $scheduleData);

        $scheduleId = $createResponse->json('schedule.id');

        // Delete the scheduled report
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/reports/schedule/' . $scheduleId);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Scheduled report deleted successfully',
                ]);
    }

    /** @test */
    public function admin_can_view_report_history_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/history');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'history' => [
                        '*' => [
                            'id',
                            'report_type',
                            'generated_at',
                            'generated_by',
                            'format',
                            'file_size',
                            'download_url',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_download_report_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/download/1');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function staff_can_view_limited_reports_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/dashboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_appointments',
                        'today_appointments',
                        'week_appointments',
                        'month_appointments',
                        'total_revenue',
                        'today_revenue',
                        'week_revenue',
                        'month_revenue',
                    ],
                ]);
    }

    /** @test */
    public function staff_cannot_view_financial_reports_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/financial');

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function client_cannot_access_reports_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/dashboard');

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_reports_api()
    {
        $response = $this->getJson('/api/reports/dashboard');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function admin_can_view_report_analytics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/analytics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'analytics' => [
                        'report_usage',
                        'popular_reports',
                        'report_generation_times',
                        'user_engagement',
                        'data_quality_metrics',
                        'performance_indicators',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_report_insights_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/reports/insights');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'insights' => [
                        'key_insights',
                        'trends',
                        'anomalies',
                        'recommendations',
                        'alerts',
                        'forecasts',
                    ],
                ]);
    }
}
