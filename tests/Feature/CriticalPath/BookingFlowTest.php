<?php

namespace Tests\Feature\CriticalPath;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

/**
 * Critical Path Test: Complete Booking Flow
 *
 * This test ensures the entire booking flow works end-to-end:
 * 1. User selects a service
 * 2. User selects date and time
 * 3. User provides contact information
 * 4. Booking is created
 * 5. Confirmation is sent
 */
class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $location;
    protected $service;
    protected $staff;
    protected $availableDate;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->location = Location::factory()->create([
            'name' => 'Main Salon',
            'is_active' => true,
        ]);

        $this->service = Service::factory()->create([
            'name' => 'Haircut',
            'duration_minutes' => 60,
            'price' => 50.00,
            'is_active' => true,
        ]);

        $staffUser = User::factory()->create([
            'name' => 'Jane Stylist',
            'email' => 'jane@salon.test',
            'is_active' => true,
        ]);

        $this->staff = Staff::factory()->create([
            'user_id' => $staffUser->id,
            'is_active' => true,
        ]);

        // Attach service to staff
        $this->staff->services()->attach($this->service->id);

        // Set available date (tomorrow at 10 AM)
        $this->availableDate = Carbon::tomorrow()->setTime(10, 0);

        Notification::fake();
    }

    /** @test */
    public function complete_booking_flow_works_for_new_customer()
    {
        // Step 1: Visit booking page
        $response = $this->get('/book');
        $response->assertStatus(200);
        $response->assertSee('Book an Appointment');

        // Step 2: User can see available services
        $response->assertSee($this->service->name);
        $response->assertSee('$' . number_format($this->service->price, 2));

        // Step 3: Create booking with all required information
        $bookingData = [
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'location_id' => $this->location->id,
            'appointment_date' => $this->availableDate->format('Y-m-d'),
            'appointment_time' => $this->availableDate->format('H:i'),
            'clientName' => 'John Doe',
            'clientEmail' => 'john@example.com',
            'clientPhone' => '+1234567890',
            'clientNotes' => 'First time customer',
        ];

        // Step 4: Submit booking
        $response = $this->post('/book', $bookingData);

        // Step 5: Verify booking was created
        $this->assertDatabaseHas('appointments', [
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'location_id' => $this->location->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'john@example.com',
        ]);

        // Step 6: Verify redirect or success message
        $response->assertSessionHas('success');
    }

    /** @test */
    public function booking_flow_works_for_returning_customer()
    {
        // Create existing client
        $user = User::factory()->create([
            'email' => 'returning@example.com',
            'is_active' => true,
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
        ]);

        // Authenticate as client
        $this->actingAs($user);

        // Book appointment
        $response = $this->post('/book', [
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'location_id' => $this->location->id,
            'appointment_date' => $this->availableDate->format('Y-m-d'),
            'appointment_time' => $this->availableDate->format('H:i'),
            'clientNotes' => 'Regular customer',
        ]);

        // Verify appointment was created with existing client
        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'service_id' => $this->service->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function booking_validates_required_fields()
    {
        $response = $this->post('/book', [
            // Missing required fields
        ]);

        $response->assertSessionHasErrors(['service_id', 'appointment_date', 'clientName', 'clientEmail']);
    }

    /** @test */
    public function booking_prevents_double_booking()
    {
        // Create existing appointment
        $existingClient = Client::factory()->create();

        Appointment::factory()->create([
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'client_id' => $existingClient->id,
            'appointment_date' => $this->availableDate,
            'status' => 'confirmed',
        ]);

        // Try to book same time slot
        $response = $this->post('/book', [
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'location_id' => $this->location->id,
            'appointment_date' => $this->availableDate->format('Y-m-d'),
            'appointment_time' => $this->availableDate->format('H:i'),
            'clientName' => 'Another Customer',
            'clientEmail' => 'another@example.com',
            'clientPhone' => '+1234567891',
        ]);

        // Should show error about unavailable time slot
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function booking_respects_business_hours()
    {
        // Try to book outside business hours (3 AM)
        $invalidTime = Carbon::tomorrow()->setTime(3, 0);

        $response = $this->post('/book', [
            'service_id' => $this->service->id,
            'staff_id' => $this->staff->id,
            'location_id' => $this->location->id,
            'appointment_date' => $invalidTime->format('Y-m-d'),
            'appointment_time' => $invalidTime->format('H:i'),
            'clientName' => 'John Doe',
            'clientEmail' => 'john@example.com',
            'clientPhone' => '+1234567890',
        ]);

        // Should fail validation or show error
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function booking_can_be_cancelled_by_customer()
    {
        // Create appointment
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'appointment_date' => $this->availableDate,
            'status' => 'confirmed',
        ]);

        // Authenticate as client
        $this->actingAs($user);

        // Cancel appointment
        $response = $this->post("/appointments/{$appointment->id}/cancel");

        // Verify cancellation
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    /** @test */
    public function booking_can_be_rescheduled()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'appointment_date' => $this->availableDate,
            'status' => 'confirmed',
        ]);

        $this->actingAs($user);

        // Reschedule to different time
        $newDate = Carbon::tomorrow()->addDays(1)->setTime(14, 0);

        $response = $this->put("/appointments/{$appointment->id}/reschedule", [
            'appointment_date' => $newDate->format('Y-m-d'),
            'appointment_time' => $newDate->format('H:i'),
        ]);

        // Verify new date
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'appointment_date' => $newDate->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    public function booking_confirmation_can_be_retrieved()
    {
        $appointment = Appointment::factory()->create([
            'staff_id' => $this->staff->id,
            'service_id' => $this->service->id,
            'appointment_date' => $this->availableDate,
            'status' => 'confirmed',
        ]);

        // Try to access with appointment ID and email
        $response = $this->post('/manage-booking', [
            'appointmentId' => $appointment->id,
            'clientEmail' => $appointment->client->user->email,
        ]);

        $response->assertStatus(200);
        $response->assertSee($appointment->service->name);
        $response->assertSee($appointment->staff->user->name);
    }
}
