<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class LocationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function location_creation_performance_test()
    {
        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Location creation should complete within 3 seconds for 100 locations');
        $this->assertEquals(100, Location::count());
    }

    /** @test */
    public function location_retrieval_performance_test()
    {
        // Create 1000 locations
        for ($i = 0; $i < 1000; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Retrieve all locations
        $locations = Location::orderBy('name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Location retrieval should complete within 2 seconds for 1000 locations');
        $this->assertEquals(1000, $locations->count());
    }

    /** @test */
    public function location_search_performance_test()
    {
        // Create 1000 locations
        for ($i = 0; $i < 1000; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Search locations by name
        $locations = Location::where('name', 'like', '%Location 1%')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Location search should complete within 1 second for 1000 locations');
        $this->assertGreaterThan(0, $locations->count());
    }

    /** @test */
    public function location_pagination_performance_test()
    {
        // Create 1000 locations
        for ($i = 0; $i < 1000; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Paginate locations
        $locations = Location::orderBy('name')->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Location pagination should complete within 1 second for 1000 locations');
        $this->assertEquals(50, $locations->count());
    }

    /** @test */
    public function location_update_performance_test()
    {
        // Create 100 locations
        $locations = [];
        for ($i = 0; $i < 100; $i++) {
            $locations[] = Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Update all locations
        foreach ($locations as $location) {
            $location->update([
                'capacity' => $location->capacity + 10,
                'notes' => 'Updated location notes',
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Location updates should complete within 3 seconds for 100 locations');
        $this->assertEquals(100, Location::where('notes', 'Updated location notes')->count());
    }

    /** @test */
    public function location_bulk_update_performance_test()
    {
        // Create 100 locations
        $locationIds = [];
        for ($i = 0; $i < 100; $i++) {
            $location = Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
            $locationIds[] = $location->id;
        }

        $startTime = microtime(true);

        // Bulk update locations
        Location::whereIn('id', $locationIds)
            ->update([
                'is_active' => false,
                'notes' => 'Bulk updated location',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk location updates should complete within 1 second for 100 locations');
        $this->assertEquals(100, Location::where('is_active', false)->count());
    }

    /** @test */
    public function location_deletion_performance_test()
    {
        // Create 100 locations
        $locations = [];
        for ($i = 0; $i < 100; $i++) {
            $locations[] = Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Soft delete all locations
        foreach ($locations as $location) {
            $location->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Location deletions should complete within 2 seconds for 100 locations');
        $this->assertEquals(0, Location::count());
        $this->assertEquals(100, Location::withTrashed()->count());
    }

    /** @test */
    public function location_bulk_deletion_performance_test()
    {
        // Create 100 locations
        $locationIds = [];
        for ($i = 0; $i < 100; $i++) {
            $location = Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
            $locationIds[] = $location->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete locations
        Location::whereIn('id', $locationIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk location deletions should complete within 1 second for 100 locations');
        $this->assertEquals(0, Location::count());
        $this->assertEquals(100, Location::withTrashed()->count());
    }

    /** @test */
    public function location_staff_performance_test()
    {
        // Create 100 locations
        $locations = [];
        for ($i = 0; $i < 100; $i++) {
            $locations[] = Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        // Create 1000 staff members for these locations
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $locations[$i % 100]->id,
                'is_active' => true,
                'specialties' => ['Hair Cutting', 'Coloring'],
                'certifications' => ['Cosmetology License'],
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
            ]);
        }

        $startTime = microtime(true);

        // Get staff for all locations
        $locationsWithStaff = Location::with(['staff' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Location staff retrieval should complete within 3 seconds for 100 locations with 1000 staff members');
        $this->assertEquals(100, $locationsWithStaff->count());
    }

    /** @test */
    public function location_statistics_performance_test()
    {
        // Create 1000 locations with different cities
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
        for ($i = 0; $i < 1000; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => $cities[$i % 5],
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => $i % 2 === 0,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Calculate statistics
        $statistics = [
            'total_locations' => Location::count(),
            'active_locations' => Location::where('is_active', true)->count(),
            'inactive_locations' => Location::where('is_active', false)->count(),
            'locations_by_city' => Location::select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->get()
                ->pluck('count', 'city'),
            'total_capacity' => Location::sum('capacity'),
            'average_capacity' => Location::avg('capacity'),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Location statistics calculation should complete within 1 second for 1000 locations');
        $this->assertEquals(1000, $statistics['total_locations']);
        $this->assertEquals(500, $statistics['active_locations']);
        $this->assertEquals(500, $statistics['inactive_locations']);
        $this->assertEquals(5, $statistics['locations_by_city']->count());
    }

    /** @test */
    public function location_database_query_performance_test()
    {
        // Create 1000 locations
        for ($i = 0; $i < 1000; $i++) {
            Location::create([
                'name' => 'Location ' . $i,
                'address' => '123 Main St ' . $i,
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+123456789' . $i,
                'email' => 'location' . $i . '@example.com',
                'is_active' => true,
                'capacity' => 50 + $i,
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
                'notes' => 'Performance test location ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $locations = Location::where('is_active', true)
            ->where('capacity', '>=', 100)
            ->where('capacity', '<=', 500)
            ->orderBy('capacity', 'desc')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(1.0, $executionTime, 'Complex location query should complete within 1 second for 1000 locations');
        $this->assertLessThan(5, $queryCount, 'Complex location query should use less than 5 database queries');
        $this->assertGreaterThan(0, $locations->count());
    }
}
