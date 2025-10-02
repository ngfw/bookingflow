<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function admin_can_create_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs($admin);

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

        $response = $this->post('/admin/appointments', $appointmentData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->user_id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
        ]);
    }

    /** @test */
    public function staff_can_create_appointment()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $client = Client::factory()->create();
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs($staff);

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

        $response = $this->post('/staff/appointments', $appointmentData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->user_id,
            'staff_id' => $staffModel->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
        ]);
    }

    /** @test */
    public function client_can_create_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $this->actingAs($client);

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

        $response = $this->post('/appointments', $appointmentData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
        ]);
    }

    /** @test */
    public function admin_can_view_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.index');
    }

    /** @test */
    public function staff_can_view_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->get('/staff/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('staff.appointments.index');
    }

    /** @test */
    public function client_can_view_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->get('/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.index');
    }

    /** @test */
    public function admin_can_update_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin);

        $updateData = [
            'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated appointment',
        ];

        $response = $this->put('/admin/appointments/' . $appointment->id, $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Updated appointment',
        ]);
    }

    /** @test */
    public function staff_can_update_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $updateData = [
            'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated appointment',
        ];

        $response = $this->put('/staff/appointments/' . $appointment->id, $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Updated appointment',
        ]);
    }

    /** @test */
    public function client_can_update_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $updateData = [
            'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated appointment',
        ];

        $response = $this->put('/appointments/' . $appointment->id, $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Updated appointment',
        ]);
    }

    /** @test */
    public function admin_can_cancel_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $this->actingAs($admin);

        $response = $this->put('/admin/appointments/' . $appointment->id . '/cancel', [
            'cancellation_reason' => 'Client requested cancellation',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Client requested cancellation',
        ]);
    }

    /** @test */
    public function staff_can_cancel_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create([
            'staff_id' => $staffModel->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($staff);

        $response = $this->put('/staff/appointments/' . $appointment->id . '/cancel', [
            'cancellation_reason' => 'Staff unavailable',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Staff unavailable',
        ]);
    }

    /** @test */
    public function client_can_cancel_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($client);

        $response = $this->put('/appointments/' . $appointment->id . '/cancel', [
            'cancellation_reason' => 'Schedule conflict',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Schedule conflict',
        ]);
    }

    /** @test */
    public function admin_can_complete_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $this->actingAs($admin);

        $response = $this->put('/admin/appointments/' . $appointment->id . '/complete', [
            'completion_notes' => 'Service completed successfully',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
            'completion_notes' => 'Service completed successfully',
        ]);
    }

    /** @test */
    public function staff_can_complete_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create([
            'staff_id' => $staffModel->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($staff);

        $response = $this->put('/staff/appointments/' . $appointment->id . '/complete', [
            'completion_notes' => 'Service completed successfully',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
            'completion_notes' => 'Service completed successfully',
        ]);
    }

    /** @test */
    public function admin_can_reschedule_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $this->actingAs($admin);

        $newDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $response = $this->put('/admin/appointments/' . $appointment->id . '/reschedule', [
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Client requested reschedule',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Client requested reschedule',
        ]);
    }

    /** @test */
    public function staff_can_reschedule_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create([
            'staff_id' => $staffModel->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($staff);

        $newDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $response = $this->put('/staff/appointments/' . $appointment->id . '/reschedule', [
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Staff schedule change',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Staff schedule change',
        ]);
    }

    /** @test */
    public function client_can_reschedule_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($client);

        $newDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $response = $this->put('/appointments/' . $appointment->id . '/reschedule', [
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Schedule conflict',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $newDate,
            'reschedule_reason' => 'Schedule conflict',
        ]);
    }

    /** @test */
    public function admin_can_delete_appointment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin);

        $response = $this->delete('/admin/appointments/' . $appointment->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    /** @test */
    public function staff_can_delete_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->delete('/staff/appointments/' . $appointment->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    /** @test */
    public function client_can_delete_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->delete('/appointments/' . $appointment->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }

    /** @test */
    public function admin_can_view_appointment_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments/' . $appointment->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.show');
    }

    /** @test */
    public function staff_can_view_their_appointment_details()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        $appointment = Appointment::factory()->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->get('/staff/appointments/' . $appointment->id);

        $response->assertStatus(200);
        $response->assertViewIs('staff.appointments.show');
    }

    /** @test */
    public function client_can_view_their_appointment_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->get('/appointments/' . $appointment->id);

        $response->assertStatus(200);
        $response->assertViewIs('appointments.show');
    }

    /** @test */
    public function admin_can_filter_appointments_by_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments?status=scheduled');

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.index');
    }

    /** @test */
    public function admin_can_filter_appointments_by_date()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->create(['appointment_date' => now()->addDay()]);
        Appointment::factory()->create(['appointment_date' => now()->addWeek()]);

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments?date=' . now()->addDay()->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.index');
    }

    /** @test */
    public function admin_can_filter_appointments_by_staff()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        Appointment::factory()->create(['staff_id' => $staff1->id]);
        Appointment::factory()->create(['staff_id' => $staff2->id]);

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments?staff_id=' . $staff1->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.index');
    }

    /** @test */
    public function admin_can_filter_appointments_by_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        Appointment::factory()->create(['client_id' => $client1->user_id]);
        Appointment::factory()->create(['client_id' => $client2->user_id]);

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments?client_id=' . $client1->user_id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.index');
    }

    /** @test */
    public function admin_can_export_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function staff_can_export_their_appointments()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        Appointment::factory()->count(3)->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->get('/staff/appointments/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function client_can_export_their_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        Appointment::factory()->count(3)->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->get('/appointments/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment1 = Appointment::factory()->create(['status' => 'scheduled']);
        $appointment2 = Appointment::factory()->create(['status' => 'scheduled']);

        $this->actingAs($admin);

        $response = $this->put('/admin/appointments/bulk-update', [
            'appointment_ids' => [$appointment1->id, $appointment2->id],
            'status' => 'completed',
        ]);

        $response->assertStatus(302);
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
    public function admin_can_bulk_delete_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment1 = Appointment::factory()->create();
        $appointment2 = Appointment::factory()->create();

        $this->actingAs($admin);

        $response = $this->delete('/admin/appointments/bulk-delete', [
            'appointment_ids' => [$appointment1->id, $appointment2->id],
        ]);

        $response->assertStatus(302);
        $this->assertSoftDeleted('appointments', ['id' => $appointment1->id]);
        $this->assertSoftDeleted('appointments', ['id' => $appointment2->id]);
    }

    /** @test */
    public function admin_can_view_appointment_calendar()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.calendar');
    }

    /** @test */
    public function staff_can_view_their_appointment_calendar()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        Appointment::factory()->count(3)->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->get('/staff/appointments/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('staff.appointments.calendar');
    }

    /** @test */
    public function client_can_view_their_appointment_calendar()
    {
        $client = User::factory()->create(['role' => 'client']);
        Appointment::factory()->count(3)->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->get('/appointments/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.calendar');
    }

    /** @test */
    public function admin_can_view_appointment_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Appointment::factory()->count(3)->create();

        $this->actingAs($admin);

        $response = $this->get('/admin/appointments/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('admin.appointments.statistics');
    }

    /** @test */
    public function staff_can_view_their_appointment_statistics()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $staffModel = Staff::factory()->create(['user_id' => $staff->id]);
        Appointment::factory()->count(3)->create(['staff_id' => $staffModel->id]);

        $this->actingAs($staff);

        $response = $this->get('/staff/appointments/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('staff.appointments.statistics');
    }

    /** @test */
    public function client_can_view_their_appointment_statistics()
    {
        $client = User::factory()->create(['role' => 'client']);
        Appointment::factory()->count(3)->create(['client_id' => $client->id]);

        $this->actingAs($client);

        $response = $this->get('/appointments/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.statistics');
    }
}
