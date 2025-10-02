<?php

namespace Tests\Dusk;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AppointmentBookingTest extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /** @test */
    public function client_can_book_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $this->browse(function ($browser) use ($client, $staff, $service, $location) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->select('staff_id', $staff->id)
                    ->select('location_id', $location->id)
                    ->type('appointment_date', now()->addDay()->format('Y-m-d'))
                    ->select('appointment_time', '10:00')
                    ->type('notes', 'Test appointment booking')
                    ->press('BOOK APPOINTMENT')
                    ->assertSee('Appointment booked successfully')
                    ->assertPathIs('/appointments');
        });
    }

    /** @test */
    public function client_can_view_available_time_slots()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();

        $this->browse(function ($browser) use ($client, $staff, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->select('staff_id', $staff->id)
                    ->type('appointment_date', now()->addDay()->format('Y-m-d'))
                    ->waitFor('@time-slots')
                    ->assertSee('Available Time Slots')
                    ->assertSee('10:00')
                    ->assertSee('11:00')
                    ->assertSee('12:00');
        });
    }

    /** @test */
    public function client_can_filter_services_by_category()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $category = \App\Models\Category::factory()->create(['name' => 'Hair Services']);
        $service1 = Service::factory()->create(['category_id' => $category->id, 'name' => 'Hair Cut']);
        $service2 = Service::factory()->create(['name' => 'Facial']);

        $this->browse(function ($browser) use ($client, $category) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('category_id', $category->id)
                    ->waitFor('@service-options')
                    ->assertSee('Hair Cut')
                    ->assertDontSee('Facial');
        });
    }

    /** @test */
    public function client_can_filter_staff_by_service()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create();
        $staff1 = Staff::factory()->create();
        $staff2 = Staff::factory()->create();
        $service->staff()->attach($staff1->id);

        $this->browse(function ($browser) use ($client, $service, $staff1) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->waitFor('@staff-options')
                    ->assertSee($staff1->user->name)
                    ->assertDontSee($staff2->user->name);
        });
    }

    /** @test */
    public function client_can_view_appointment_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments')
                    ->click('@appointment-' . $appointment->id)
                    ->assertSee('Appointment Details')
                    ->assertSee($appointment->service->name)
                    ->assertSee($appointment->staff->user->name);
        });
    }

    /** @test */
    public function client_can_reschedule_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'scheduled',
            'appointment_date' => now()->addDay(),
        ]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@reschedule-button')
                    ->type('appointment_date', now()->addDays(2)->format('Y-m-d'))
                    ->select('appointment_time', '14:00')
                    ->type('reschedule_reason', 'Schedule conflict')
                    ->press('RESCHEDULE APPOINTMENT')
                    ->assertSee('Appointment rescheduled successfully');
        });
    }

    /** @test */
    public function client_can_cancel_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'scheduled',
            'appointment_date' => now()->addDay(),
        ]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@cancel-button')
                    ->type('cancellation_reason', 'Schedule conflict')
                    ->press('CANCEL APPOINTMENT')
                    ->assertSee('Appointment cancelled successfully');
        });
    }

    /** @test */
    public function client_can_view_appointment_calendar()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments/calendar')
                    ->assertSee('Appointment Calendar')
                    ->assertSee('Today')
                    ->assertSee('This Week')
                    ->assertSee('This Month');
        });
    }

    /** @test */
    public function client_can_filter_appointments_by_status()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $scheduledAppointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'scheduled',
        ]);
        $completedAppointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'completed',
        ]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments')
                    ->select('status', 'scheduled')
                    ->press('FILTER')
                    ->assertSee('Scheduled')
                    ->assertDontSee('Completed');
        });
    }

    /** @test */
    public function client_can_search_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service1 = Service::factory()->create(['name' => 'Hair Cut']);
        $service2 = Service::factory()->create(['name' => 'Facial']);
        $appointment1 = Appointment::factory()->create([
            'client_id' => $client->id,
            'service_id' => $service1->id,
        ]);
        $appointment2 = Appointment::factory()->create([
            'client_id' => $client->id,
            'service_id' => $service2->id,
        ]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments')
                    ->type('search', 'Hair Cut')
                    ->press('SEARCH')
                    ->assertSee('Hair Cut')
                    ->assertDontSee('Facial');
        });
    }

    /** @test */
    public function client_can_export_appointments()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointments = Appointment::factory()->count(3)->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments')
                    ->click('@export-button')
                    ->assertSee('Export Appointments')
                    ->select('format', 'csv')
                    ->press('EXPORT')
                    ->assertSee('Export completed successfully');
        });
    }

    /** @test */
    public function client_can_view_appointment_reminders()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'appointment_date' => now()->addHour(),
            'reminder_hours' => 24,
        ]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments')
                    ->assertSee('Upcoming Appointment')
                    ->assertSee('Reminder: 1 hour');
        });
    }

    /** @test */
    public function client_can_set_appointment_reminders()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'appointment_date' => now()->addDay(),
        ]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@reminder-settings')
                    ->check('email_reminder')
                    ->check('sms_reminder')
                    ->select('reminder_hours', '48')
                    ->press('SAVE REMINDER SETTINGS')
                    ->assertSee('Reminder settings updated');
        });
    }

    /** @test */
    public function client_can_rate_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create([
            'client_id' => $client->id,
            'status' => 'completed',
        ]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@rate-appointment')
                    ->select('rating', '5')
                    ->type('review', 'Excellent service!')
                    ->press('SUBMIT RATING')
                    ->assertSee('Thank you for your feedback');
        });
    }

    /** @test */
    public function client_can_view_appointment_history()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $pastAppointments = Appointment::factory()->count(5)->create([
            'client_id' => $client->id,
            'status' => 'completed',
            'appointment_date' => now()->subDays(rand(1, 30)),
        ]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments/history')
                    ->assertSee('Appointment History')
                    ->assertSee('Completed Appointments');
        });
    }

    /** @test */
    public function client_can_view_appointment_statistics()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointments = Appointment::factory()->count(10)->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client) {
            $browser->loginAs($client)
                    ->visit('/appointments/statistics')
                    ->assertSee('Appointment Statistics')
                    ->assertSee('Total Appointments')
                    ->assertSee('Completed Appointments')
                    ->assertSee('Cancelled Appointments');
        });
    }

    /** @test */
    public function client_can_view_favorite_services()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create();
        $clientModel->update(['favorite_services' => [$service->id]]);

        $this->browse(function ($browser) use ($client, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->click('@favorite-services')
                    ->assertSee('Favorite Services')
                    ->assertSee($service->name);
        });
    }

    /** @test */
    public function client_can_add_service_to_favorites()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create();

        $this->browse(function ($browser) use ($client, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->click('@add-to-favorites')
                    ->assertSee('Service added to favorites');
        });
    }

    /** @test */
    public function client_can_remove_service_from_favorites()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create();
        $clientModel->update(['favorite_services' => [$service->id]]);

        $this->browse(function ($browser) use ($client, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->click('@favorite-services')
                    ->click('@remove-favorite-' . $service->id)
                    ->assertSee('Service removed from favorites');
        });
    }

    /** @test */
    public function client_can_view_recommended_services()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create(['is_popular' => true]);

        $this->browse(function ($browser) use ($client, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->click('@recommended-services')
                    ->assertSee('Recommended Services')
                    ->assertSee($service->name);
        });
    }

    /** @test */
    public function client_can_view_service_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $service = Service::factory()->create([
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
        ]);

        $this->browse(function ($browser) use ($client, $service) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->click('@service-details')
                    ->assertSee('Service Details')
                    ->assertSee('Hair Cut')
                    ->assertSee('Professional hair cutting service')
                    ->assertSee('$50.00')
                    ->assertSee('60 minutes');
        });
    }

    /** @test */
    public function client_can_view_staff_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create([
            'position' => 'Senior Stylist',
            'skills' => ['hair_cutting', 'coloring'],
        ]);

        $this->browse(function ($browser) use ($client, $staff) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('staff_id', $staff->id)
                    ->click('@staff-details')
                    ->assertSee('Staff Details')
                    ->assertSee($staff->user->name)
                    ->assertSee('Senior Stylist')
                    ->assertSee('Hair Cutting')
                    ->assertSee('Coloring');
        });
    }

    /** @test */
    public function client_can_view_location_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $location = Location::factory()->create([
            'name' => 'Downtown Salon',
            'address' => '123 Main St',
            'phone' => '555-0123',
        ]);

        $this->browse(function ($browser) use ($client, $location) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('location_id', $location->id)
                    ->click('@location-details')
                    ->assertSee('Location Details')
                    ->assertSee('Downtown Salon')
                    ->assertSee('123 Main St')
                    ->assertSee('555-0123');
        });
    }

    /** @test */
    public function client_can_view_booking_confirmation()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $this->browse(function ($browser) use ($client, $staff, $service, $location) {
            $browser->loginAs($client)
                    ->visit('/book')
                    ->select('service_id', $service->id)
                    ->select('staff_id', $staff->id)
                    ->select('location_id', $location->id)
                    ->type('appointment_date', now()->addDay()->format('Y-m-d'))
                    ->select('appointment_time', '10:00')
                    ->press('BOOK APPOINTMENT')
                    ->assertSee('Booking Confirmation')
                    ->assertSee('Appointment Details')
                    ->assertSee($service->name)
                    ->assertSee($staff->user->name)
                    ->assertSee($location->name);
        });
    }

    /** @test */
    public function client_can_print_appointment_confirmation()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@print-confirmation')
                    ->assertSee('Print Appointment Confirmation');
        });
    }

    /** @test */
    public function client_can_share_appointment()
    {
        $client = User::factory()->create(['role' => 'client']);
        $clientModel = Client::factory()->create(['user_id' => $client->id]);
        $appointment = Appointment::factory()->create(['client_id' => $client->id]);

        $this->browse(function ($browser) use ($client, $appointment) {
            $browser->loginAs($client)
                    ->visit('/appointments/' . $appointment->id)
                    ->click('@share-appointment')
                    ->assertSee('Share Appointment')
                    ->assertSee('Copy Link')
                    ->assertSee('Share via Email');
        });
    }
}
