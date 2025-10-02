<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function service_creation_performance_test()
    {
        $category = Category::factory()->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Service creation should complete within 3 seconds for 100 services');
        $this->assertEquals(100, Service::count());
    }

    /** @test */
    public function service_retrieval_performance_test()
    {
        $category = Category::factory()->create();

        // Create 1000 services
        for ($i = 0; $i < 1000; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Retrieve all services
        $services = Service::orderBy('name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Service retrieval should complete within 2 seconds for 1000 services');
        $this->assertEquals(1000, $services->count());
    }

    /** @test */
    public function service_search_performance_test()
    {
        $category = Category::factory()->create();

        // Create 1000 services
        for ($i = 0; $i < 1000; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Search services by name
        $services = Service::where('name', 'like', '%Service 1%')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Service search should complete within 1 second for 1000 services');
        $this->assertGreaterThan(0, $services->count());
    }

    /** @test */
    public function service_pagination_performance_test()
    {
        $category = Category::factory()->create();

        // Create 1000 services
        for ($i = 0; $i < 1000; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Paginate services
        $services = Service::orderBy('name')->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Service pagination should complete within 1 second for 1000 services');
        $this->assertEquals(50, $services->count());
    }

    /** @test */
    public function service_update_performance_test()
    {
        $category = Category::factory()->create();

        // Create 100 services
        $services = [];
        for ($i = 0; $i < 100; $i++) {
            $services[] = Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Update all services
        foreach ($services as $service) {
            $service->update([
                'price' => $service->price + 10,
                'description' => 'Updated service description',
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Service updates should complete within 3 seconds for 100 services');
        $this->assertEquals(100, Service::where('description', 'Updated service description')->count());
    }

    /** @test */
    public function service_bulk_update_performance_test()
    {
        $category = Category::factory()->create();

        // Create 100 services
        $serviceIds = [];
        for ($i = 0; $i < 100; $i++) {
            $service = Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
            $serviceIds[] = $service->id;
        }

        $startTime = microtime(true);

        // Bulk update services
        Service::whereIn('id', $serviceIds)
            ->update([
                'is_active' => false,
                'description' => 'Bulk updated service',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk service updates should complete within 1 second for 100 services');
        $this->assertEquals(100, Service::where('is_active', false)->count());
    }

    /** @test */
    public function service_deletion_performance_test()
    {
        $category = Category::factory()->create();

        // Create 100 services
        $services = [];
        for ($i = 0; $i < 100; $i++) {
            $services[] = Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Soft delete all services
        foreach ($services as $service) {
            $service->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Service deletions should complete within 2 seconds for 100 services');
        $this->assertEquals(0, Service::count());
        $this->assertEquals(100, Service::withTrashed()->count());
    }

    /** @test */
    public function service_bulk_deletion_performance_test()
    {
        $category = Category::factory()->create();

        // Create 100 services
        $serviceIds = [];
        for ($i = 0; $i < 100; $i++) {
            $service = Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
            $serviceIds[] = $service->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete services
        Service::whereIn('id', $serviceIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk service deletions should complete within 1 second for 100 services');
        $this->assertEquals(0, Service::count());
        $this->assertEquals(100, Service::withTrashed()->count());
    }

    /** @test */
    public function service_appointment_history_performance_test()
    {
        $category = Category::factory()->create();

        // Create 100 services
        $services = [];
        for ($i = 0; $i < 100; $i++) {
            $services[] = Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        // Create 1000 appointments for these services
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => 1,
                'staff_id' => 1,
                'service_id' => $services[$i % 100]->id,
                'location_id' => 1,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'completed',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Get appointment history for all services
        $servicesWithAppointments = Service::with(['appointments' => function ($query) {
            $query->orderBy('appointment_date', 'desc');
        }])->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Service appointment history retrieval should complete within 3 seconds for 100 services with 1000 appointments');
        $this->assertEquals(100, $servicesWithAppointments->count());
    }

    /** @test */
    public function service_statistics_performance_test()
    {
        $category = Category::factory()->create();

        // Create 1000 services with different prices
        for ($i = 0; $i < 1000; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => $i % 2 === 0,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Calculate statistics
        $statistics = [
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'inactive_services' => Service::where('is_active', false)->count(),
            'average_price' => Service::avg('price'),
            'min_price' => Service::min('price'),
            'max_price' => Service::max('price'),
            'total_duration' => Service::sum('duration'),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Service statistics calculation should complete within 1 second for 1000 services');
        $this->assertEquals(1000, $statistics['total_services']);
        $this->assertEquals(500, $statistics['active_services']);
        $this->assertEquals(500, $statistics['inactive_services']);
    }

    /** @test */
    public function service_database_query_performance_test()
    {
        $category = Category::factory()->create();

        // Create 1000 services
        for ($i = 0; $i < 1000; $i++) {
            Service::create([
                'name' => 'Service ' . $i,
                'description' => 'Professional service ' . $i,
                'price' => 50.00 + $i,
                'duration' => 60 + $i,
                'category_id' => $category->id,
                'is_active' => true,
                'requires_staff' => true,
                'max_clients' => 1,
                'booking_advance_days' => 30,
                'cancellation_hours' => 24,
            ]);
        }

        $startTime = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $services = Service::where('is_active', true)
            ->where('price', '>=', 100)
            ->where('price', '<=', 500)
            ->orderBy('price')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(1.0, $executionTime, 'Complex service query should complete within 1 second for 1000 services');
        $this->assertLessThan(5, $queryCount, 'Complex service query should use less than 5 database queries');
        $this->assertGreaterThan(0, $services->count());
    }
}
