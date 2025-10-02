<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $serviceData = [
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
            'category_id' => $category->id,
            'is_active' => true,
            'requires_staff' => true,
            'max_clients' => 1,
            'booking_advance_days' => 30,
            'cancellation_hours' => 24,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/services', $serviceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration',
                        'category_id',
                        'is_active',
                        'requires_staff',
                        'max_clients',
                        'booking_advance_days',
                        'cancellation_hours',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function staff_can_create_service_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $serviceData = [
            'name' => 'Manicure',
            'description' => 'Professional nail care service',
            'price' => 30.00,
            'duration' => 45,
            'category_id' => $category->id,
            'is_active' => true,
            'requires_staff' => true,
            'max_clients' => 1,
            'booking_advance_days' => 30,
            'cancellation_hours' => 24,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/services', $serviceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration',
                        'category_id',
                        'is_active',
                        'requires_staff',
                        'max_clients',
                        'booking_advance_days',
                        'cancellation_hours',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_services_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $services = Service::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'duration',
                            'category_id',
                            'is_active',
                            'requires_staff',
                            'max_clients',
                            'booking_advance_days',
                            'cancellation_hours',
                            'created_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);
    }

    /** @test */
    public function staff_can_view_services_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $services = Service::factory()->count(3)->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'duration',
                            'category_id',
                            'is_active',
                            'requires_staff',
                            'max_clients',
                            'booking_advance_days',
                            'cancellation_hours',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_can_view_active_services_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        Service::factory()->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'services')
                ->assertJsonPath('services.0.is_active', true);
    }

    /** @test */
    public function admin_can_view_service_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/' . $service->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration',
                        'category_id',
                        'is_active',
                        'requires_staff',
                        'max_clients',
                        'booking_advance_days',
                        'cancellation_hours',
                        'created_at',
                        'category' => [
                            'id',
                            'name',
                            'description',
                        ],
                        'appointments' => [
                            '*' => [
                                'id',
                                'appointment_date',
                                'status',
                                'client' => [
                                    'id',
                                    'name',
                                    'email',
                                ],
                                'staff' => [
                                    'id',
                                    'name',
                                    'position',
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Service Name',
            'price' => 75.00,
            'duration' => 90,
            'description' => 'Updated service description',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/services/' . $service->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'service' => [
                        'id' => $service->id,
                        'name' => 'Updated Service Name',
                        'price' => 75.00,
                        'duration' => 90,
                        'description' => 'Updated service description',
                    ],
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
            'price' => 75.00,
            'duration' => 90,
            'description' => 'Updated service description',
        ]);
    }

    /** @test */
    public function staff_can_update_service_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $service = Service::factory()->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Staff Updated Service',
            'price' => 65.00,
            'duration' => 75,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/services/' . $service->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'service' => [
                        'id' => $service->id,
                        'name' => 'Staff Updated Service',
                        'price' => 65.00,
                        'duration' => 75,
                    ],
                ]);
    }

    /** @test */
    public function admin_can_delete_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/services/' . $service->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Service deleted successfully',
                ]);

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    /** @test */
    public function admin_can_search_services_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->create(['name' => 'Hair Cut']);
        Service::factory()->create(['name' => 'Manicure']);
        Service::factory()->create(['name' => 'Facial']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services?search=Hair');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'services')
                ->assertJsonPath('services.0.name', 'Hair Cut');
    }

    /** @test */
    public function admin_can_filter_services_by_category_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category1 = Category::factory()->create(['name' => 'Hair Services']);
        $category2 = Category::factory()->create(['name' => 'Nail Services']);
        Service::factory()->create(['category_id' => $category1->id]);
        Service::factory()->create(['category_id' => $category2->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services?category_id=' . $category1->id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'services')
                ->assertJsonPath('services.0.category_id', $category1->id);
    }

    /** @test */
    public function admin_can_filter_services_by_price_range_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->create(['price' => 30.00]);
        Service::factory()->create(['price' => 50.00]);
        Service::factory()->create(['price' => 80.00]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services?min_price=40&max_price=60');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'services')
                ->assertJsonPath('services.0.price', 50.00);
    }

    /** @test */
    public function admin_can_filter_services_by_duration_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->create(['duration' => 30]);
        Service::factory()->create(['duration' => 60]);
        Service::factory()->create(['duration' => 90]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services?min_duration=45&max_duration=75');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'services')
                ->assertJsonPath('services.0.duration', 60);
    }

    /** @test */
    public function admin_can_export_services_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_services_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/services/bulk-update', [
            'service_ids' => [$service1->id, $service2->id],
            'is_active' => false,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Services updated successfully',
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $service2->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_services_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/services/bulk-delete', [
            'service_ids' => [$service1->id, $service2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Services deleted successfully',
                ]);

        $this->assertSoftDeleted('services', ['id' => $service1->id]);
        $this->assertSoftDeleted('services', ['id' => $service2->id]);
    }

    /** @test */
    public function admin_can_view_service_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_services',
                        'active_services',
                        'inactive_services',
                        'services_by_category',
                        'average_price',
                        'most_popular_services',
                        'revenue_by_service',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_service_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['service_id' => $service->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/' . $service->id . '/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'appointment_date',
                            'status',
                            'client' => [
                                'id',
                                'name',
                                'email',
                            ],
                            'staff' => [
                                'id',
                                'name',
                                'position',
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_service_revenue_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/' . $service->id . '/revenue');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'revenue' => [
                        'total_revenue',
                        'monthly_revenue',
                        'yearly_revenue',
                        'revenue_by_month',
                        'average_revenue_per_appointment',
                    ],
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_services_api()
    {
        $response = $this->getJson('/api/services');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function client_can_view_service_details_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $service = Service::factory()->create(['is_active' => true]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/' . $service->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration',
                        'category_id',
                        'is_active',
                        'requires_staff',
                        'max_clients',
                        'booking_advance_days',
                        'cancellation_hours',
                        'created_at',
                        'category' => [
                            'id',
                            'name',
                            'description',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_cannot_view_inactive_service_details_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $service = Service::factory()->create(['is_active' => false]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/services/' . $service->id);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Service not found',
                ]);
    }

    /** @test */
    public function admin_can_duplicate_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/services/' . $service->id . '/duplicate');

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration',
                        'category_id',
                        'is_active',
                        'requires_staff',
                        'max_clients',
                        'booking_advance_days',
                        'cancellation_hours',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('services', [
            'name' => $service->name . ' (Copy)',
            'description' => $service->description,
            'price' => $service->price,
            'duration' => $service->duration,
        ]);
    }

    /** @test */
    public function admin_can_archive_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_active' => true]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/services/' . $service->id . '/archive');

        $response->assertStatus(200)
                ->assertJson([
                    'service' => [
                        'id' => $service->id,
                        'is_active' => false,
                    ],
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_restore_service_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/services/' . $service->id . '/restore');

        $response->assertStatus(200)
                ->assertJson([
                    'service' => [
                        'id' => $service->id,
                        'is_active' => true,
                    ],
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'is_active' => true,
        ]);
    }
}
