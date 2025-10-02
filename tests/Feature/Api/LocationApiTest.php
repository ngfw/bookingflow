<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $locationData = [
            'name' => 'Downtown Salon',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'phone' => '+1234567890',
            'email' => 'downtown@salon.com',
            'is_active' => true,
            'capacity' => 50,
            'operating_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '10:00', 'close' => '16:00'],
                'sunday' => ['open' => '10:00', 'close' => '16:00'],
            ],
            'amenities' => ['WiFi', 'Parking', 'Waiting Area'],
            'notes' => 'Main location with full services',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/locations', $locationData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'location' => [
                        'id',
                        'name',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'phone',
                        'email',
                        'is_active',
                        'capacity',
                        'operating_hours',
                        'amenities',
                        'notes',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'Downtown Salon',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
        ]);
    }

    /** @test */
    public function admin_can_view_locations_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $locations = Location::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'locations' => [
                        '*' => [
                            'id',
                            'name',
                            'address',
                            'city',
                            'state',
                            'zip_code',
                            'phone',
                            'email',
                            'is_active',
                            'capacity',
                            'operating_hours',
                            'amenities',
                            'notes',
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
    public function staff_can_view_locations_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $locations = Location::factory()->count(3)->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'locations' => [
                        '*' => [
                            'id',
                            'name',
                            'address',
                            'city',
                            'state',
                            'zip_code',
                            'phone',
                            'email',
                            'is_active',
                            'capacity',
                            'operating_hours',
                            'amenities',
                            'notes',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_can_view_active_locations_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        Location::factory()->create(['is_active' => true]);
        Location::factory()->create(['is_active' => false]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'locations')
                ->assertJsonPath('locations.0.is_active', true);
    }

    /** @test */
    public function admin_can_view_location_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'location' => [
                        'id',
                        'name',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'phone',
                        'email',
                        'is_active',
                        'capacity',
                        'operating_hours',
                        'amenities',
                        'notes',
                        'created_at',
                        'staff' => [
                            '*' => [
                                'id',
                                'name',
                                'position',
                                'is_active',
                            ],
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
                                'service' => [
                                    'id',
                                    'name',
                                    'price',
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Location Name',
            'address' => '456 New St',
            'phone' => '+9876543210',
            'capacity' => 75,
            'amenities' => ['WiFi', 'Parking', 'Waiting Area', 'Coffee Bar'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/locations/' . $location->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'location' => [
                        'id' => $location->id,
                        'name' => 'Updated Location Name',
                        'address' => '456 New St',
                        'phone' => '+9876543210',
                        'capacity' => 75,
                    ],
                ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Updated Location Name',
            'address' => '456 New St',
            'phone' => '+9876543210',
            'capacity' => 75,
        ]);
    }

    /** @test */
    public function admin_can_delete_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/locations/' . $location->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Location deleted successfully',
                ]);

        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }

    /** @test */
    public function admin_can_search_locations_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->create(['name' => 'Downtown Salon']);
        Location::factory()->create(['name' => 'Uptown Spa']);
        Location::factory()->create(['name' => 'Midtown Beauty']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations?search=Downtown');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'locations')
                ->assertJsonPath('locations.0.name', 'Downtown Salon');
    }

    /** @test */
    public function admin_can_filter_locations_by_city_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->create(['city' => 'New York']);
        Location::factory()->create(['city' => 'Los Angeles']);
        Location::factory()->create(['city' => 'Chicago']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations?city=New York');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'locations')
                ->assertJsonPath('locations.0.city', 'New York');
    }

    /** @test */
    public function admin_can_filter_locations_by_state_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->create(['state' => 'NY']);
        Location::factory()->create(['state' => 'CA']);
        Location::factory()->create(['state' => 'IL']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations?state=NY');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'locations')
                ->assertJsonPath('locations.0.state', 'NY');
    }

    /** @test */
    public function admin_can_filter_locations_by_status_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->create(['is_active' => true]);
        Location::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations?is_active=true');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'locations')
                ->assertJsonPath('locations.0.is_active', true);
    }

    /** @test */
    public function admin_can_export_locations_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_locations_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location1 = Location::factory()->create();
        $location2 = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/locations/bulk-update', [
            'location_ids' => [$location1->id, $location2->id],
            'is_active' => false,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Locations updated successfully',
                ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('locations', [
            'id' => $location2->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_locations_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location1 = Location::factory()->create();
        $location2 = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/locations/bulk-delete', [
            'location_ids' => [$location1->id, $location2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Locations deleted successfully',
                ]);

        $this->assertSoftDeleted('locations', ['id' => $location1->id]);
        $this->assertSoftDeleted('locations', ['id' => $location2->id]);
    }

    /** @test */
    public function admin_can_view_location_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Location::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_locations',
                        'active_locations',
                        'inactive_locations',
                        'locations_by_city',
                        'locations_by_state',
                        'total_capacity',
                        'average_capacity',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_location_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['location_id' => $location->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id . '/appointments');

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
                            'service' => [
                                'id',
                                'name',
                                'price',
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_location_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $staff = Staff::factory()->count(3)->create(['location_id' => $location->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id . '/staff');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'staff' => [
                        '*' => [
                            'id',
                            'name',
                            'position',
                            'is_active',
                            'hire_date',
                            'salary',
                            'commission_rate',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_location_revenue_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id . '/revenue');

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
    public function admin_can_view_location_capacity_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id . '/capacity');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'capacity' => [
                        'total_capacity',
                        'current_occupancy',
                        'available_capacity',
                        'capacity_utilization',
                        'peak_hours',
                        'capacity_by_day',
                    ],
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_locations_api()
    {
        $response = $this->getJson('/api/locations');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function client_can_view_location_details_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $location = Location::factory()->create(['is_active' => true]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'location' => [
                        'id',
                        'name',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'phone',
                        'email',
                        'is_active',
                        'capacity',
                        'operating_hours',
                        'amenities',
                        'notes',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function client_cannot_view_inactive_location_details_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $location = Location::factory()->create(['is_active' => false]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locations/' . $location->id);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Location not found',
                ]);
    }

    /** @test */
    public function admin_can_archive_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create(['is_active' => true]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/locations/' . $location->id . '/archive');

        $response->assertStatus(200)
                ->assertJson([
                    'location' => [
                        'id' => $location->id,
                        'is_active' => false,
                    ],
                ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_restore_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/locations/' . $location->id . '/restore');

        $response->assertStatus(200)
                ->assertJson([
                    'location' => [
                        'id' => $location->id,
                        'is_active' => true,
                    ],
                ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'is_active' => true,
        ]);
    }
}
