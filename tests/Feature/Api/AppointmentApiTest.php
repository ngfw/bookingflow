<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $appointmentData = [
            'client_id' => $client->user_id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
            'appointment_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration' => 60,
            'price' => 50.00,
            'notes' => 'Test appointment',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'appointment' => [
                        'id',
                        'client_id',
                        'staff_id',
                        'service_id',
                        'location_id',
                        'appointment_date',
                        'duration',
                        'price',
                        'status',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->user_id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
        ]);
    }

    /** @test */
    public function staff_can_create_appointment_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $client = Client::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $appointmentData = [
            'client_id' => $client->user_id,
            'staff_id' => $staffModel->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
            'appointment_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration' => 60,
            'price' => 50.00,
            'notes' => 'Test appointment',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'appointment' => [
                        'id',
                        'client_id',
                        'staff_id',
                        'service_id',
                        'location_id',
                        'appointment_date',
                        'duration',
                        'price',
                        'status',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function client_can_create_appointment_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();
        $token = $client->createToken('test-token')->plainTextToken;

        $appointmentData = [
            'client_id' => $client->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
            'appointment_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration' => 60,
            'price' => 50.00,
            'notes' => 'Test appointment',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'appointment' => [
                        'id',
                        'client_id',
                        'staff_id',
                        'service_id',
                        'location_id',
                        'appointment_date',
                        'duration',
                        'price',
                        'status',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointments = Appointment::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'client_id',
                            'staff_id',
                            'service_id',
                            'location_id',
                            'appointment_date',
                            'duration',
                            'price',
                            'status',
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
    public function staff_can_view_their_appointments_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointments = Appointment::factory()->count(3)->create(['staff_id' => $staffModel->id]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'client_id',
                            'staff_id',
                            'service_id',
                            'location_id',
                            'appointment_date',
                            'duration',
                            'price',
                            'status',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function client_can_view_their_appointments_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointments' => [
                        '*' => [
                            'id',
                            'client_id',
                            'staff_id',
                            'service_id',
                            'location_id',
                            'appointment_date',
                            'duration',
                            'price',
                            'status',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_appointment_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'appointment' => [
                        'id',
                        'client_id',
                        'staff_id',
                        'service_id',
                        'location_id',
                        'appointment_date',
                        'duration',
                        'price',
                        'status',
                        'notes',
                        'created_at',
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
                        'location' => [
                            'id',
                            'name',
                            'address',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated appointment',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/appointments/' . $appointment->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'appointment' => [
                        'id' => $appointment->id,
                        'notes' => 'Updated appointment',
                    ],
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Updated appointment',
        ]);
    }

    /** @test */
    public function admin_can_cancel_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/appointments/' . $appointment->id . '/cancel', [
            'cancellation_reason' => 'Client requested cancellation',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'appointment' => [
                        'id' => $appointment->id,
                        'status' => 'cancelled',
                        'cancellation_reason' => 'Client requested cancellation',
                    ],
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Client requested cancellation',
        ]);
    }

    /** @test */
    public function admin_can_complete_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/appointments/' . $appointment->id . '/complete', [
            'completion_notes' => 'Service completed successfully',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'appointment' => [
                        'id' => $appointment->id,
                        'status' => 'completed',
                        'completion_notes' => 'Service completed successfully',
                    ],
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
            'completion_notes' => 'Service completed successfully',
        ]);
    }

    /** @test */
    public function admin_can_reschedule_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $newDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/appointments/' . $appointment->id . '/reschedule', [
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Client requested reschedule',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'appointment' => [
                        'id' => $appointment->id,
                        'appointment_date' => $newDate,
                        'reschedule_reason' => 'Client requested reschedule',
                    ],
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Client requested reschedule',
        ]);
    }

    /** @test */
    public function admin_can_delete_appointment_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Appointment deleted successfully',
                ]);

        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    /** @test */
    public function admin_can_filter_appointments_by_status_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments?status=scheduled');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'appointments')
                ->assertJsonPath('appointments.0.status', 'scheduled');
    }

    /** @test */
    public function admin_can_filter_appointments_by_date_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->create(['appointment_date' => now()->addDay()]);
        Appointment::factory()->create(['appointment_date' => now()->addWeek()]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments?date=' . now()->addDay()->format('Y-m-d'));

        $response->assertStatus(200)
                ->assertJsonCount(1, 'appointments');
    }

    /** @test */
    public function admin_can_filter_appointments_by_staff_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        Appointment::factory()->create(['staff_id' => $staff1->id]);
        Appointment::factory()->create(['staff_id' => $staff2->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments?staff_id=' . $staff1->id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'appointments')
                ->assertJsonPath('appointments.0.staff_id', $staff1->id);
    }

    /** @test */
    public function admin_can_filter_appointments_by_client_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        Appointment::factory()->create(['client_id' => $client1->user_id]);
        Appointment::factory()->create(['client_id' => $client2->user_id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments?client_id=' . $client1->user_id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'appointments')
                ->assertJsonPath('appointments.0.client_id', $client1->user_id);
    }

    /** @test */
    public function admin_can_export_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment1 = Appointment::factory()->create(['status' => 'scheduled']);
        $appointment2 = Appointment::factory()->create(['status' => 'scheduled']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/appointments/bulk-update', [
            'appointment_ids' => [$appointment1->id, $appointment2->id],
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Appointments updated successfully',
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment1->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment2->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_appointments_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment1 = Appointment::factory()->create();
        $appointment2 = Appointment::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/appointments/bulk-delete', [
            'appointment_ids' => [$appointment1->id, $appointment2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Appointments deleted successfully',
                ]);

        $this->assertSoftDeleted('appointments', ['id' => $appointment1->id]);
        $this->assertSoftDeleted('appointments', ['id' => $appointment2->id]);
    }

    /** @test */
    public function admin_can_view_appointment_calendar_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/calendar');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'calendar' => [
                        '*' => [
                            'date',
                            'appointments' => [
                                '*' => [
                                    'id',
                                    'client_id',
                                    'staff_id',
                                    'service_id',
                                    'appointment_date',
                                    'duration',
                                    'status',
                                ],
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_appointment_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_appointments',
                        'scheduled_appointments',
                        'completed_appointments',
                        'cancelled_appointments',
                        'today_appointments',
                        'week_appointments',
                        'month_appointments',
                    ],
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_appointments_api()
    {
        $response = $this->getJson('/api/appointments');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function user_cannot_access_other_users_appointments()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create(['client_id' => $client2->id]);
        $token = $client1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function staff_cannot_access_other_staff_appointments()
    {
        $staff1 = User::factory()->create(['role' => 'staff']);
        $staff2 = User::factory()->create(['role' => 'staff']);
        $staffModel1 = Staff::factory()->create(['user_id' => $staff1->id]);
        $staffModel2 = Staff::factory()->create(['user_id' => $staff2->id]);
        $appointment = Appointment::factory()->create(['staff_id' => $staffModel2->id]);
        $token = $staff1->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/' . $appointment->id);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function client_can_rate_appointment_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'completed',
        ]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/appointments/' . $appointment->id . '/rate', [
            'rating' => 5,
            'review' => 'Excellent service!',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Rating submitted successfully',
                ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'rating' => 5,
            'review' => 'Excellent service!',
        ]);
    }

    /** @test */
    public function client_can_view_available_time_slots_via_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/appointments/available-slots', [
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'available_slots' => [
                        '*' => [
                            'time',
                            'available',
                        ],
                    ],
                ]);
    }
}
