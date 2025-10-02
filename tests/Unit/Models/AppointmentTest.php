<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_an_appointment()
    {
        $appointmentData = [
            'client_id' => 1,
            'staff_id' => 1,
            'service_id' => 1,
            'location_id' => 1,
            'appointment_date' => now()->addDay(),
            'duration' => 60,
            'status' => 'scheduled',
            'notes' => 'Test appointment',
        ];

        $appointment = Appointment::create($appointmentData);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals(1, $appointment->client_id);
        $this->assertEquals(1, $appointment->staff_id);
        $this->assertEquals(1, $appointment->service_id);
        $this->assertEquals('scheduled', $appointment->status);
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $client = Client::factory()->create();
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(Client::class, $appointment->client);
        $this->assertEquals($client->id, $appointment->client->id);
    }

    /** @test */
    public function it_belongs_to_staff()
    {
        $staff = Staff::factory()->create();
        $appointment = Appointment::factory()->create(['staff_id' => $staff->id]);

        $this->assertInstanceOf(Staff::class, $appointment->staff);
        $this->assertEquals($staff->id, $appointment->staff->id);
    }

    /** @test */
    public function it_belongs_to_a_service()
    {
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create(['service_id' => $service->id]);

        $this->assertInstanceOf(Service::class, $appointment->service);
        $this->assertEquals($service->id, $appointment->service->id);
    }

    /** @test */
    public function it_belongs_to_a_location()
    {
        $location = Location::factory()->create();
        $appointment = Appointment::factory()->create(['location_id' => $location->id]);

        $this->assertInstanceOf(Location::class, $appointment->location);
        $this->assertEquals($location->id, $appointment->location->id);
    }

    /** @test */
    public function it_can_have_an_invoice()
    {
        $appointment = Appointment::factory()->create();
        $invoice = \App\Models\Invoice::factory()->create(['appointment_id' => $appointment->id]);

        $this->assertInstanceOf(\App\Models\Invoice::class, $appointment->invoice);
        $this->assertEquals($invoice->id, $appointment->invoice->id);
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $appointment = Appointment::factory()->create([
            'appointment_date' => '2024-12-25 10:00:00',
            'duration' => 60,
            'price' => 100.50,
            'is_recurring' => true,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $appointment->appointment_date);
        $this->assertIsInt($appointment->duration);
        $this->assertIsFloat($appointment->price);
        $this->assertIsBool($appointment->is_recurring);
    }

    /** @test */
    public function it_can_scope_scheduled_appointments()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);

        $scheduledAppointments = Appointment::where('status', 'scheduled')->get();

        $this->assertCount(1, $scheduledAppointments);
        $this->assertEquals('scheduled', $scheduledAppointments->first()->status);
    }

    /** @test */
    public function it_can_scope_completed_appointments()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);

        $completedAppointments = Appointment::where('status', 'completed')->get();

        $this->assertCount(1, $completedAppointments);
        $this->assertEquals('completed', $completedAppointments->first()->status);
    }

    /** @test */
    public function it_can_scope_cancelled_appointments()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);

        $cancelledAppointments = Appointment::where('status', 'cancelled')->get();

        $this->assertCount(1, $cancelledAppointments);
        $this->assertEquals('cancelled', $cancelledAppointments->first()->status);
    }

    /** @test */
    public function it_can_scope_appointments_for_date()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        Appointment::factory()->create(['appointment_date' => $today]);
        Appointment::factory()->create(['appointment_date' => $tomorrow]);

        $todayAppointments = Appointment::whereDate('appointment_date', $today)->get();

        $this->assertCount(1, $todayAppointments);
    }

    /** @test */
    public function it_can_scope_appointments_for_staff()
    {
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();

        Appointment::factory()->create(['staff_id' => $staff1->id]);
        Appointment::factory()->create(['staff_id' => $staff2->id]);

        $staff1Appointments = Appointment::where('staff_id', $staff1->id)->get();

        $this->assertCount(1, $staff1Appointments);
        $this->assertEquals($staff1->id, $staff1Appointments->first()->staff_id);
    }

    /** @test */
    public function it_can_scope_appointments_for_client()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        Appointment::factory()->create(['client_id' => $client1->id]);
        Appointment::factory()->create(['client_id' => $client2->id]);

        $client1Appointments = Appointment::where('client_id', $client1->id)->get();

        $this->assertCount(1, $client1Appointments);
        $this->assertEquals($client1->id, $client1Appointments->first()->client_id);
    }

    /** @test */
    public function it_can_scope_appointments_for_service()
    {
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();

        Appointment::factory()->create(['service_id' => $service1->id]);
        Appointment::factory()->create(['service_id' => $service2->id]);

        $service1Appointments = Appointment::where('service_id', $service1->id)->get();

        $this->assertCount(1, $service1Appointments);
        $this->assertEquals($service1->id, $service1Appointments->first()->service_id);
    }

    /** @test */
    public function it_can_scope_appointments_for_location()
    {
        $location1 = Location::factory()->create();
        $location2 = Location::factory()->create();

        Appointment::factory()->create(['location_id' => $location1->id]);
        Appointment::factory()->create(['location_id' => $location2->id]);

        $location1Appointments = Appointment::where('location_id', $location1->id)->get();

        $this->assertCount(1, $location1Appointments);
        $this->assertEquals($location1->id, $location1Appointments->first()->location_id);
    }

    /** @test */
    public function it_can_scope_upcoming_appointments()
    {
        $pastAppointment = Appointment::factory()->create(['appointment_date' => now()->subDay()]);
        $upcomingAppointment = Appointment::factory()->create(['appointment_date' => now()->addDay()]);

        $upcomingAppointments = Appointment::where('appointment_date', '>', now())->get();

        $this->assertCount(1, $upcomingAppointments);
        $this->assertEquals($upcomingAppointment->id, $upcomingAppointments->first()->id);
    }

    /** @test */
    public function it_can_scope_past_appointments()
    {
        $pastAppointment = Appointment::factory()->create(['appointment_date' => now()->subDay()]);
        $upcomingAppointment = Appointment::factory()->create(['appointment_date' => now()->addDay()]);

        $pastAppointments = Appointment::where('appointment_date', '<', now())->get();

        $this->assertCount(1, $pastAppointments);
        $this->assertEquals($pastAppointment->id, $pastAppointments->first()->id);
    }

    /** @test */
    public function it_can_scope_recurring_appointments()
    {
        Appointment::factory()->create(['is_recurring' => true]);
        Appointment::factory()->create(['is_recurring' => false]);

        $recurringAppointments = Appointment::where('is_recurring', true)->get();

        $this->assertCount(1, $recurringAppointments);
        $this->assertTrue($recurringAppointments->first()->is_recurring);
    }

    /** @test */
    public function it_can_scope_appointments_by_status()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);

        $scheduledAppointments = Appointment::where('status', 'scheduled')->get();
        $completedAppointments = Appointment::where('status', 'completed')->get();
        $cancelledAppointments = Appointment::where('status', 'cancelled')->get();

        $this->assertCount(1, $scheduledAppointments);
        $this->assertCount(1, $completedAppointments);
        $this->assertCount(1, $cancelledAppointments);
    }

    /** @test */
    public function it_can_get_appointment_duration_in_hours()
    {
        $appointment = Appointment::factory()->create(['duration' => 120]);

        $this->assertEquals(2, $appointment->duration_in_hours);
    }

    /** @test */
    public function it_can_get_appointment_duration_in_minutes()
    {
        $appointment = Appointment::factory()->create(['duration' => 90]);

        $this->assertEquals(90, $appointment->duration_in_minutes);
    }

    /** @test */
    public function it_can_get_appointment_end_time()
    {
        $appointment = Appointment::factory()->create([
            'appointment_date' => now()->setTime(10, 0),
            'duration' => 60,
        ]);

        $expectedEndTime = now()->setTime(11, 0);
        $this->assertEquals($expectedEndTime->format('H:i'), $appointment->end_time->format('H:i'));
    }

    /** @test */
    public function it_can_check_if_appointment_is_upcoming()
    {
        $upcomingAppointment = Appointment::factory()->create(['appointment_date' => now()->addHour()]);
        $pastAppointment = Appointment::factory()->create(['appointment_date' => now()->subHour()]);

        $this->assertTrue($upcomingAppointment->is_upcoming);
        $this->assertFalse($pastAppointment->is_upcoming);
    }

    /** @test */
    public function it_can_check_if_appointment_is_past()
    {
        $upcomingAppointment = Appointment::factory()->create(['appointment_date' => now()->addHour()]);
        $pastAppointment = Appointment::factory()->create(['appointment_date' => now()->subHour()]);

        $this->assertFalse($upcomingAppointment->is_past);
        $this->assertTrue($pastAppointment->is_past);
    }

    /** @test */
    public function it_can_check_if_appointment_is_today()
    {
        $todayAppointment = Appointment::factory()->create(['appointment_date' => now()]);
        $tomorrowAppointment = Appointment::factory()->create(['appointment_date' => now()->addDay()]);

        $this->assertTrue($todayAppointment->is_today);
        $this->assertFalse($tomorrowAppointment->is_today);
    }

    /** @test */
    public function it_can_check_if_appointment_is_overdue()
    {
        $overdueAppointment = Appointment::factory()->create([
            'appointment_date' => now()->subHour(),
            'status' => 'scheduled',
        ]);
        $completedAppointment = Appointment::factory()->create([
            'appointment_date' => now()->subHour(),
            'status' => 'completed',
        ]);

        $this->assertTrue($overdueAppointment->is_overdue);
        $this->assertFalse($completedAppointment->is_overdue);
    }

    /** @test */
    public function it_can_get_appointment_status_display()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $this->assertEquals('Scheduled', $appointment->status_display);
    }

    /** @test */
    public function it_can_get_appointment_status_color()
    {
        $scheduledAppointment = Appointment::factory()->create(['status' => 'scheduled']);
        $completedAppointment = Appointment::factory()->create(['status' => 'completed']);
        $cancelledAppointment = Appointment::factory()->create(['status' => 'cancelled']);

        $this->assertEquals('blue', $scheduledAppointment->status_color);
        $this->assertEquals('green', $completedAppointment->status_color);
        $this->assertEquals('red', $cancelledAppointment->status_color);
    }

    /** @test */
    public function it_can_cancel_appointment()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $appointment->cancel('Client requested cancellation');

        $this->assertEquals('cancelled', $appointment->status);
        $this->assertEquals('Client requested cancellation', $appointment->cancellation_reason);
        $this->assertNotNull($appointment->cancelled_at);
    }

    /** @test */
    public function it_can_complete_appointment()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $appointment->complete('Service completed successfully');

        $this->assertEquals('completed', $appointment->status);
        $this->assertEquals('Service completed successfully', $appointment->completion_notes);
        $this->assertNotNull($appointment->completed_at);
    }

    /** @test */
    public function it_can_reschedule_appointment()
    {
        $appointment = Appointment::factory()->create([
            'appointment_date' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $newDate = now()->addDays(2);
        $appointment->reschedule($newDate, 'Client requested reschedule');

        $this->assertEquals($newDate->format('Y-m-d H:i:s'), $appointment->appointment_date->format('Y-m-d H:i:s'));
        $this->assertEquals('Client requested reschedule', $appointment->reschedule_reason);
    }

    /** @test */
    public function it_can_get_appointment_total_price()
    {
        $appointment = Appointment::factory()->create([
            'price' => 100.00,
            'tax_amount' => 10.00,
            'discount_amount' => 5.00,
        ]);

        $expectedTotal = 100.00 + 10.00 - 5.00;
        $this->assertEquals($expectedTotal, $appointment->total_price);
    }

    /** @test */
    public function it_can_get_appointment_discount_percentage()
    {
        $appointment = Appointment::factory()->create([
            'price' => 100.00,
            'discount_amount' => 10.00,
        ]);

        $this->assertEquals(10, $appointment->discount_percentage);
    }

    /** @test */
    public function it_can_get_appointment_tax_percentage()
    {
        $appointment = Appointment::factory()->create([
            'price' => 100.00,
            'tax_amount' => 8.00,
        ]);

        $this->assertEquals(8, $appointment->tax_percentage);
    }

    /** @test */
    public function it_can_get_appointment_reminder_time()
    {
        $appointment = Appointment::factory()->create([
            'appointment_date' => now()->addDay()->setTime(10, 0),
            'reminder_hours' => 24,
        ]);

        $expectedReminderTime = now()->addDay()->setTime(10, 0)->subHours(24);
        $this->assertEquals($expectedReminderTime->format('Y-m-d H:i:s'), $appointment->reminder_time->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_check_if_reminder_is_due()
    {
        $appointment = Appointment::factory()->create([
            'appointment_date' => now()->addHour(),
            'reminder_hours' => 24,
        ]);

        $this->assertFalse($appointment->is_reminder_due);

        $appointment->update(['appointment_date' => now()->addMinutes(30)]);

        $this->assertTrue($appointment->is_reminder_due);
    }

    /** @test */
    public function it_can_get_appointment_confirmation_code()
    {
        $appointment = Appointment::factory()->create();

        $this->assertNotNull($appointment->confirmation_code);
        $this->assertIsString($appointment->confirmation_code);
        $this->assertEquals(8, strlen($appointment->confirmation_code));
    }

    /** @test */
    public function it_can_validate_appointment_date()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Appointment::factory()->create(['appointment_date' => null]);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Appointment::create([]);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $appointment = Appointment::factory()->create();
        $appointmentId = $appointment->id;

        $appointment->delete();

        $this->assertSoftDeleted('appointments', ['id' => $appointmentId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_appointment()
    {
        $appointment = Appointment::factory()->create();
        $appointment->delete();

        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);

        $appointment->restore();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $appointment = Appointment::factory()->create();
        $appointmentId = $appointment->id;

        $appointment->forceDelete();

        $this->assertDatabaseMissing('appointments', ['id' => $appointmentId]);
    }

    /** @test */
    public function it_can_get_appointment_statistics()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);
        Appointment::factory()->create(['status' => 'cancelled']);

        $stats = [
            'total' => Appointment::count(),
            'scheduled' => Appointment::where('status', 'scheduled')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['scheduled']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['cancelled']);
    }
}
