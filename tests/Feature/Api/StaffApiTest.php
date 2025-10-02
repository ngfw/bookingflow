<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaffApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $staffData = [
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'position' => 'Hair Stylist',
            'hire_date' => '2023-01-01',
            'salary' => 50000.00,
            'commission_rate' => 10.0,
            'location_id' => $location->id,
            'is_active' => true,
            'specialties' => ['Hair Cutting', 'Coloring'],
            'certifications' => ['Cosmetology License'],
            'emergency_contact' => 'Jane Smith',
            'emergency_phone' => '+1234567891',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/staff', $staffData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'staff' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'position',
                        'hire_date',
                        'salary',
                        'commission_rate',
                        'location_id',
                        'is_active',
                        'specialties',
                        'certifications',
                        'emergency_contact',
                        'emergency_phone',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('staff', [
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'position' => 'Hair Stylist',
        ]);
    }

    /** @test */
    public function admin_can_view_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'staff' => [
                        '*' => [
                            'id',
                            'user_id',
                            'name',
                            'email',
                            'phone',
                            'position',
                            'hire_date',
                            'salary',
                            'commission_rate',
                            'location_id',
                            'is_active',
                            'specialties',
                            'certifications',
                            'emergency_contact',
                            'emergency_phone',
                            'address',
                            'city',
                            'state',
                            'zip_code',
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
    public function staff_can_view_own_profile_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staffModel->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'staff' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'position',
                        'hire_date',
                        'salary',
                        'commission_rate',
                        'location_id',
                        'is_active',
                        'specialties',
                        'certifications',
                        'emergency_contact',
                        'emergency_phone',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_staff_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staff->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'staff' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'position',
                        'hire_date',
                        'salary',
                        'commission_rate',
                        'location_id',
                        'is_active',
                        'specialties',
                        'certifications',
                        'emergency_contact',
                        'emergency_phone',
                        'address',
                        'city',
                        'state',
                        'zip_code',
                        'created_at',
                        'location' => [
                            'id',
                            'name',
                            'address',
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
    public function admin_can_update_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Staff Name',
            'position' => 'Senior Hair Stylist',
            'salary' => 60000.00,
            'commission_rate' => 15.0,
            'specialties' => ['Hair Cutting', 'Coloring', 'Styling'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/staff/' . $staff->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'staff' => [
                        'id' => $staff->id,
                        'name' => 'Updated Staff Name',
                        'position' => 'Senior Hair Stylist',
                        'salary' => 60000.00,
                        'commission_rate' => 15.0,
                    ],
                ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'name' => 'Updated Staff Name',
            'position' => 'Senior Hair Stylist',
            'salary' => 60000.00,
            'commission_rate' => 15.0,
        ]);
    }

    /** @test */
    public function staff_can_update_own_profile_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Staff Name',
            'phone' => '+9876543210',
            'address' => '456 New St',
            'specialties' => ['Hair Cutting', 'Coloring'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/staff/' . $staffModel->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'staff' => [
                        'id' => $staffModel->id,
                        'name' => 'Updated Staff Name',
                        'phone' => '+9876543210',
                        'address' => '456 New St',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_delete_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/staff/' . $staff->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Staff deleted successfully',
                ]);

        $this->assertSoftDeleted('staff', ['id' => $staff->id]);
    }

    /** @test */
    public function admin_can_search_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Staff::factory()->create(['name' => 'John Smith']);
        Staff::factory()->create(['name' => 'Jane Doe']);
        Staff::factory()->create(['name' => 'Bob Johnson']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff?search=John');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'staff')
                ->assertJsonPath('staff.0.name', 'John Smith');
    }

    /** @test */
    public function admin_can_filter_staff_by_position_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Staff::factory()->create(['position' => 'Hair Stylist']);
        Staff::factory()->create(['position' => 'Nail Technician']);
        Staff::factory()->create(['position' => 'Esthetician']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff?position=Hair Stylist');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'staff')
                ->assertJsonPath('staff.0.position', 'Hair Stylist');
    }

    /** @test */
    public function admin_can_filter_staff_by_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location1 = Location::factory()->create(['name' => 'Downtown']);
        $location2 = Location::factory()->create(['name' => 'Uptown']);
        Staff::factory()->create(['location_id' => $location1->id]);
        Staff::factory()->create(['location_id' => $location2->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff?location_id=' . $location1->id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'staff')
                ->assertJsonPath('staff.0.location_id', $location1->id);
    }

    /** @test */
    public function admin_can_filter_staff_by_status_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Staff::factory()->create(['is_active' => true]);
        Staff::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff?is_active=true');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'staff')
                ->assertJsonPath('staff.0.is_active', true);
    }

    /** @test */
    public function admin_can_export_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Staff::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/staff/bulk-update', [
            'staff_ids' => [$staff1->id, $staff2->id],
            'is_active' => false,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Staff updated successfully',
                ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('staff', [
            'id' => $staff2->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/staff/bulk-delete', [
            'staff_ids' => [$staff1->id, $staff2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Staff deleted successfully',
                ]);

        $this->assertSoftDeleted('staff', ['id' => $staff1->id]);
        $this->assertSoftDeleted('staff', ['id' => $staff2->id]);
    }

    /** @test */
    public function admin_can_view_staff_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Staff::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_staff',
                        'active_staff',
                        'inactive_staff',
                        'staff_by_position',
                        'staff_by_location',
                        'average_salary',
                        'total_commission_paid',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_staff_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['staff_id' => $staff->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staff->id . '/appointments');

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
    public function admin_can_view_staff_performance_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staff->id . '/performance');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'performance' => [
                        'total_appointments',
                        'completed_appointments',
                        'cancelled_appointments',
                        'total_revenue',
                        'commission_earned',
                        'average_rating',
                        'performance_by_month',
                        'top_services',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_staff_schedule_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staff->id . '/schedule');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'schedule' => [
                        '*' => [
                            'date',
                            'appointments' => [
                                '*' => [
                                    'id',
                                    'appointment_date',
                                    'duration',
                                    'status',
                                    'client' => [
                                        'id',
                                        'name',
                                    ],
                                    'service' => [
                                        'id',
                                        'name',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function staff_can_view_own_schedule_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staffModel->id . '/schedule');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'schedule' => [
                        '*' => [
                            'date',
                            'appointments' => [
                                '*' => [
                                    'id',
                                    'appointment_date',
                                    'duration',
                                    'status',
                                    'client' => [
                                        'id',
                                        'name',
                                    ],
                                    'service' => [
                                        'id',
                                        'name',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function staff_can_view_own_performance_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staffModel->id . '/performance');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'performance' => [
                        'total_appointments',
                        'completed_appointments',
                        'cancelled_appointments',
                        'total_revenue',
                        'commission_earned',
                        'average_rating',
                        'performance_by_month',
                        'top_services',
                    ],
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_staff_api()
    {
        $response = $this->getJson('/api/staff');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function user_cannot_access_other_staff_profile()
    {
        $staff1 = User::factory()->create(['role' => 'staff']);
        $staff2 = User::factory()->create(['role' => 'staff']);
        $staffModel2 = Staff::factory()->create(['user_id' => $staff2->id]);
        $token = $staff1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/staff/' . $staffModel2->id);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function admin_can_archive_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create(['is_active' => true]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/staff/' . $staff->id . '/archive');

        $response->assertStatus(200)
                ->assertJson([
                    'staff' => [
                        'id' => $staff->id,
                        'is_active' => false,
                    ],
                ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_restore_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = Staff::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/staff/' . $staff->id . '/restore');

        $response->assertStatus(200)
                ->assertJson([
                    'staff' => [
                        'id' => $staff->id,
                        'is_active' => true,
                    ],
                ]);

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'is_active' => true,
        ]);
    }
}
