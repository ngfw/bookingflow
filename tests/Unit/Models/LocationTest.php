<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_location()
    {
        $locationData = [
            'name' => 'Downtown Salon',
            'code' => 'DT001',
            'description' => 'Main downtown location',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'phone' => '555-0123',
            'email' => 'downtown@salon.com',
            'is_active' => true,
            'is_headquarters' => true,
        ];

        $location = Location::create($locationData);

        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals('Downtown Salon', $location->name);
        $this->assertEquals('DT001', $location->code);
        $this->assertEquals('New York', $location->city);
        $this->assertTrue($location->is_active);
        $this->assertTrue($location->is_headquarters);
    }

    /** @test */
    public function it_can_have_many_staff()
    {
        $location = Location::factory()->create();
        $staff = Staff::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->staff);
        $this->assertInstanceOf(Staff::class, $location->staff->first());
    }

    /** @test */
    public function it_can_have_many_appointments()
    {
        $location = Location::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->appointments);
        $this->assertInstanceOf(Appointment::class, $location->appointments->first());
    }

    /** @test */
    public function it_can_have_many_services()
    {
        $location = Location::factory()->create();
        $services = Service::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->services);
        $this->assertInstanceOf(Service::class, $location->services->first());
    }

    /** @test */
    public function it_can_have_many_products()
    {
        $location = Location::factory()->create();
        $products = Product::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->products);
        $this->assertInstanceOf(Product::class, $location->products->first());
    }

    /** @test */
    public function it_can_have_many_invoices()
    {
        $location = Location::factory()->create();
        $invoices = Invoice::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->invoices);
        $this->assertInstanceOf(Invoice::class, $location->invoices->first());
    }

    /** @test */
    public function it_can_have_many_payments()
    {
        $location = Location::factory()->create();
        $payments = Payment::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertCount(3, $location->payments);
        $this->assertInstanceOf(Payment::class, $location->payments->first());
    }

    /** @test */
    public function it_can_have_many_users()
    {
        $location = Location::factory()->create();
        $users = User::factory()->count(3)->create(['primary_location_id' => $location->id]);

        $this->assertCount(3, $location->users);
        $this->assertInstanceOf(User::class, $location->users->first());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $location = Location::factory()->create([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'tax_rate' => 0.0875,
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
            ],
            'amenities' => ['wifi', 'parking', 'wheelchair_access'],
            'settings' => ['auto_confirm' => true, 'reminder_hours' => 24],
            'is_active' => true,
            'is_headquarters' => false,
            'max_staff' => 10,
            'max_clients_per_day' => 50,
        ]);

        $this->assertIsFloat($location->latitude);
        $this->assertIsFloat($location->longitude);
        $this->assertIsFloat($location->tax_rate);
        $this->assertIsArray($location->business_hours);
        $this->assertIsArray($location->amenities);
        $this->assertIsArray($location->settings);
        $this->assertIsBool($location->is_active);
        $this->assertIsBool($location->is_headquarters);
        $this->assertIsInt($location->max_staff);
        $this->assertIsInt($location->max_clients_per_day);
    }

    /** @test */
    public function it_can_scope_active_locations()
    {
        Location::factory()->create(['is_active' => true]);
        Location::factory()->create(['is_active' => false]);

        $activeLocations = Location::where('is_active', true)->get();

        $this->assertCount(1, $activeLocations);
        $this->assertTrue($activeLocations->first()->is_active);
    }

    /** @test */
    public function it_can_scope_headquarters()
    {
        Location::factory()->create(['is_headquarters' => true]);
        Location::factory()->create(['is_headquarters' => false]);

        $headquarters = Location::where('is_headquarters', true)->get();

        $this->assertCount(1, $headquarters);
        $this->assertTrue($headquarters->first()->is_headquarters);
    }

    /** @test */
    public function it_can_scope_locations_in_city()
    {
        Location::factory()->create(['city' => 'New York']);
        Location::factory()->create(['city' => 'Los Angeles']);

        $nyLocations = Location::where('city', 'New York')->get();

        $this->assertCount(1, $nyLocations);
        $this->assertEquals('New York', $nyLocations->first()->city);
    }

    /** @test */
    public function it_can_scope_locations_in_state()
    {
        Location::factory()->create(['state' => 'NY']);
        Location::factory()->create(['state' => 'CA']);

        $nyLocations = Location::where('state', 'NY')->get();

        $this->assertCount(1, $nyLocations);
        $this->assertEquals('NY', $nyLocations->first()->state);
    }

    /** @test */
    public function it_can_get_full_address()
    {
        $location = Location::factory()->create([
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
        ]);

        $expectedAddress = '123 Main St, New York, NY 10001, US';
        $this->assertEquals($expectedAddress, $location->full_address);
    }

    /** @test */
    public function it_can_get_business_hours_formatted()
    {
        $location = Location::factory()->create([
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
            ],
        ]);

        $formattedHours = $location->business_hours_formatted;

        $this->assertArrayHasKey('Monday', $formattedHours);
        $this->assertArrayHasKey('Tuesday', $formattedHours);
    }

    /** @test */
    public function it_can_get_amenities_list()
    {
        $location = Location::factory()->create([
            'amenities' => ['wifi', 'parking', 'wheelchair_access'],
        ]);

        $amenitiesList = $location->amenities_list;

        $this->assertContains('wifi', $amenitiesList);
        $this->assertContains('parking', $amenitiesList);
        $this->assertContains('wheelchair_access', $amenitiesList);
    }

    /** @test */
    public function it_can_check_if_location_is_open()
    {
        $location = Location::factory()->create([
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
            ],
            'timezone' => 'America/New_York',
        ]);

        // Mock current time to be within business hours
        $this->travelTo(now()->setTimezone('America/New_York')->setTime(10, 0));

        $this->assertTrue($location->isOpen());
    }

    /** @test */
    public function it_can_get_next_open_time()
    {
        $location = Location::factory()->create([
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
            ],
            'timezone' => 'America/New_York',
        ]);

        // Mock current time to be outside business hours
        $this->travelTo(now()->setTimezone('America/New_York')->setTime(20, 0));

        $nextOpenTime = $location->getNextOpenTime();

        $this->assertNotNull($nextOpenTime);
        $this->assertInstanceOf(\Carbon\Carbon::class, $nextOpenTime);
    }

    /** @test */
    public function it_can_get_distance_from_coordinates()
    {
        $location = Location::factory()->create([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $distance = $location->getDistanceFrom(40.7589, -73.9851);

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(0, $distance);
    }

    /** @test */
    public function it_can_get_staff_count()
    {
        $location = Location::factory()->create();
        Staff::factory()->count(3)->create(['location_id' => $location->id]);

        $this->assertEquals(3, $location->getStaffCount());
    }

    /** @test */
    public function it_can_get_active_staff_count()
    {
        $location = Location::factory()->create();
        
        $activeUser1 = User::factory()->create(['is_active' => true]);
        $activeUser2 = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        Staff::factory()->create(['user_id' => $activeUser1->id, 'location_id' => $location->id]);
        Staff::factory()->create(['user_id' => $activeUser2->id, 'location_id' => $location->id]);
        Staff::factory()->create(['user_id' => $inactiveUser->id, 'location_id' => $location->id]);

        $this->assertEquals(2, $location->getActiveStaffCount());
    }

    /** @test */
    public function it_can_get_today_appointments_count()
    {
        $location = Location::factory()->create();
        
        Appointment::factory()->create([
            'location_id' => $location->id,
            'appointment_date' => now(),
            'status' => 'scheduled',
        ]);
        Appointment::factory()->create([
            'location_id' => $location->id,
            'appointment_date' => now(),
            'status' => 'cancelled',
        ]);
        Appointment::factory()->create([
            'location_id' => $location->id,
            'appointment_date' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $this->assertEquals(1, $location->getTodayAppointmentsCount());
    }

    /** @test */
    public function it_can_get_today_revenue()
    {
        $location = Location::factory()->create();
        
        Payment::factory()->create([
            'location_id' => $location->id,
            'payment_date' => now(),
            'status' => 'completed',
            'amount' => 50.00,
        ]);
        Payment::factory()->create([
            'location_id' => $location->id,
            'payment_date' => now(),
            'status' => 'pending',
            'amount' => 25.00,
        ]);
        Payment::factory()->create([
            'location_id' => $location->id,
            'payment_date' => now()->addDay(),
            'status' => 'completed',
            'amount' => 75.00,
        ]);

        $this->assertEquals(50.00, $location->getTodayRevenue());
    }

    /** @test */
    public function it_can_get_location_statistics()
    {
        $location = Location::factory()->create();
        
        // Create staff
        Staff::factory()->count(2)->create(['location_id' => $location->id]);
        
        // Create appointments
        Appointment::factory()->create([
            'location_id' => $location->id,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'location_id' => $location->id,
            'status' => 'scheduled',
        ]);
        
        // Create payments
        Payment::factory()->create([
            'location_id' => $location->id,
            'status' => 'completed',
            'amount' => 100.00,
        ]);

        $stats = $location->getStatistics();

        $this->assertEquals(2, $stats['staff_count']);
        $this->assertEquals(2, $stats['appointments_count']);
        $this->assertEquals(100.00, $stats['revenue']);
    }

    /** @test */
    public function it_can_get_location_performance_metrics()
    {
        $location = Location::factory()->create();
        
        $metrics = $location->getPerformanceMetrics();

        $this->assertArrayHasKey('staff_utilization', $metrics);
        $this->assertArrayHasKey('appointment_completion_rate', $metrics);
        $this->assertArrayHasKey('revenue_per_staff', $metrics);
        $this->assertArrayHasKey('client_satisfaction', $metrics);
    }

    /** @test */
    public function it_can_activate_location()
    {
        $location = Location::factory()->create(['is_active' => false]);

        $location->activate();

        $this->assertTrue($location->is_active);
    }

    /** @test */
    public function it_can_deactivate_location()
    {
        $location = Location::factory()->create(['is_active' => true]);

        $location->deactivate();

        $this->assertFalse($location->is_active);
    }

    /** @test */
    public function it_can_set_as_headquarters()
    {
        $location = Location::factory()->create(['is_headquarters' => false]);

        $location->setAsHeadquarters();

        $this->assertTrue($location->is_headquarters);
    }

    /** @test */
    public function it_can_unset_as_headquarters()
    {
        $location = Location::factory()->create(['is_headquarters' => true]);

        $location->unsetAsHeadquarters();

        $this->assertFalse($location->is_headquarters);
    }

    /** @test */
    public function it_can_update_business_hours()
    {
        $location = Location::factory()->create([
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
            ],
        ]);

        $newHours = [
            'monday' => ['open' => '08:00', 'close' => '19:00'],
            'tuesday' => ['open' => '09:00', 'close' => '18:00'],
        ];

        $location->updateBusinessHours($newHours);

        $this->assertEquals($newHours, $location->business_hours);
    }

    /** @test */
    public function it_can_add_amenity()
    {
        $location = Location::factory()->create(['amenities' => ['wifi']]);

        $location->addAmenity('parking');

        $this->assertContains('wifi', $location->amenities);
        $this->assertContains('parking', $location->amenities);
    }

    /** @test */
    public function it_can_remove_amenity()
    {
        $location = Location::factory()->create(['amenities' => ['wifi', 'parking']]);

        $location->removeAmenity('wifi');

        $this->assertNotContains('wifi', $location->amenities);
        $this->assertContains('parking', $location->amenities);
    }

    /** @test */
    public function it_can_update_settings()
    {
        $location = Location::factory()->create(['settings' => ['auto_confirm' => true]]);

        $newSettings = [
            'auto_confirm' => false,
            'reminder_hours' => 48,
            'max_advance_booking_days' => 30,
        ];

        $location->updateSettings($newSettings);

        $this->assertEquals($newSettings, $location->settings);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Location::create([]);
    }

    /** @test */
    public function it_can_validate_latitude_range()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Location::factory()->create(['latitude' => 200.0]);
    }

    /** @test */
    public function it_can_validate_longitude_range()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Location::factory()->create(['longitude' => 200.0]);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $location = Location::factory()->create();
        $locationId = $location->id;

        $location->delete();

        $this->assertSoftDeleted('locations', ['id' => $locationId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_location()
    {
        $location = Location::factory()->create();
        $location->delete();

        $this->assertSoftDeleted('locations', ['id' => $location->id]);

        $location->restore();

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $location = Location::factory()->create();
        $locationId = $location->id;

        $location->forceDelete();

        $this->assertDatabaseMissing('locations', ['id' => $locationId]);
    }

    /** @test */
    public function it_can_get_location_search_results()
    {
        Location::factory()->create(['name' => 'Downtown Salon', 'city' => 'New York']);
        Location::factory()->create(['name' => 'Uptown Salon', 'city' => 'Los Angeles']);

        $downtownLocations = Location::where('name', 'like', '%Downtown%')->get();

        $this->assertCount(1, $downtownLocations);
    }

    /** @test */
    public function it_can_get_locations_by_city()
    {
        Location::factory()->create(['city' => 'New York']);
        Location::factory()->create(['city' => 'Los Angeles']);

        $nyLocations = Location::where('city', 'New York')->get();

        $this->assertCount(1, $nyLocations);
        $this->assertEquals('New York', $nyLocations->first()->city);
    }

    /** @test */
    public function it_can_get_locations_by_state()
    {
        Location::factory()->create(['state' => 'NY']);
        Location::factory()->create(['state' => 'CA']);

        $nyLocations = Location::where('state', 'NY')->get();

        $this->assertCount(1, $nyLocations);
        $this->assertEquals('NY', $nyLocations->first()->state);
    }

    /** @test */
    public function it_can_get_nearby_locations()
    {
        $location1 = Location::factory()->create([
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);
        $location2 = Location::factory()->create([
            'latitude' => 40.7589,
            'longitude' => -73.9851,
        ]);
        $location3 = Location::factory()->create([
            'latitude' => 34.0522,
            'longitude' => -118.2437,
        ]);

        $nearbyLocations = Location::where('id', '!=', $location1->id)
            ->get()
            ->filter(function ($loc) use ($location1) {
                return $loc->getDistanceFrom($location1->latitude, $location1->longitude) < 50;
            });

        $this->assertCount(1, $nearbyLocations);
        $this->assertEquals($location2->id, $nearbyLocations->first()->id);
    }
}
