<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class AppointmentPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function appointment_creation_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5.0, $executionTime, 'Appointment creation should complete within 5 seconds for 100 appointments');
        $this->assertEquals(100, Appointment::count());
    }

    /** @test */
    public function appointment_retrieval_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Retrieve all appointments with relationships
        $appointments = Appointment::with(['client', 'staff', 'service', 'location'])
            ->orderBy('appointment_date')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Appointment retrieval should complete within 2 seconds for 1000 appointments');
        $this->assertEquals(1000, $appointments->count());
    }

    /** @test */
    public function appointment_search_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Search appointments by status
        $appointments = Appointment::where('status', 'scheduled')
            ->with(['client', 'staff', 'service', 'location'])
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Appointment search should complete within 1 second for 1000 appointments');
        $this->assertEquals(1000, $appointments->count());
    }

    /** @test */
    public function appointment_pagination_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Paginate appointments
        $appointments = Appointment::with(['client', 'staff', 'service', 'location'])
            ->orderBy('appointment_date')
            ->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Appointment pagination should complete within 1 second for 1000 appointments');
        $this->assertEquals(50, $appointments->count());
    }

    /** @test */
    public function appointment_update_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 100 appointments
        $appointments = [];
        for ($i = 0; $i < 100; $i++) {
            $appointments[] = Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Update all appointments
        foreach ($appointments as $appointment) {
            $appointment->update([
                'status' => 'completed',
                'completion_notes' => 'Updated via performance test',
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Appointment updates should complete within 3 seconds for 100 appointments');
        $this->assertEquals(100, Appointment::where('status', 'completed')->count());
    }

    /** @test */
    public function appointment_bulk_update_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 100 appointments
        $appointmentIds = [];
        for ($i = 0; $i < 100; $i++) {
            $appointment = Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
            $appointmentIds[] = $appointment->id;
        }

        $startTime = microtime(true);

        // Bulk update appointments
        Appointment::whereIn('id', $appointmentIds)
            ->update([
                'status' => 'completed',
                'completion_notes' => 'Bulk updated via performance test',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk appointment updates should complete within 1 second for 100 appointments');
        $this->assertEquals(100, Appointment::where('status', 'completed')->count());
    }

    /** @test */
    public function appointment_deletion_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 100 appointments
        $appointments = [];
        for ($i = 0; $i < 100; $i++) {
            $appointments[] = Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Soft delete all appointments
        foreach ($appointments as $appointment) {
            $appointment->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Appointment deletions should complete within 2 seconds for 100 appointments');
        $this->assertEquals(0, Appointment::count());
        $this->assertEquals(100, Appointment::withTrashed()->count());
    }

    /** @test */
    public function appointment_bulk_deletion_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 100 appointments
        $appointmentIds = [];
        for ($i = 0; $i < 100; $i++) {
            $appointment = Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
            $appointmentIds[] = $appointment->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete appointments
        Appointment::whereIn('id', $appointmentIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk appointment deletions should complete within 1 second for 100 appointments');
        $this->assertEquals(0, Appointment::count());
        $this->assertEquals(100, Appointment::withTrashed()->count());
    }

    /** @test */
    public function appointment_calendar_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments across different dates
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Get calendar data for a month
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $appointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->with(['client', 'staff', 'service', 'location'])
            ->orderBy('appointment_date')
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Appointment calendar generation should complete within 2 seconds for 1000 appointments');
        $this->assertGreaterThan(0, $appointments->count());
    }

    /** @test */
    public function appointment_statistics_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments with different statuses
        $statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => $statuses[$i % 4],
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Calculate statistics
        $statistics = [
            'total_appointments' => Appointment::count(),
            'scheduled_appointments' => Appointment::where('status', 'scheduled')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'no_show_appointments' => Appointment::where('status', 'no_show')->count(),
            'total_revenue' => Appointment::where('status', 'completed')->sum('price'),
            'average_price' => Appointment::where('status', 'completed')->avg('price'),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Appointment statistics calculation should complete within 1 second for 1000 appointments');
        $this->assertEquals(1000, $statistics['total_appointments']);
        $this->assertEquals(250, $statistics['scheduled_appointments']);
        $this->assertEquals(250, $statistics['completed_appointments']);
        $this->assertEquals(250, $statistics['cancelled_appointments']);
        $this->assertEquals(250, $statistics['no_show_appointments']);
    }

    /** @test */
    public function appointment_database_query_performance_test()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $staff = Staff::factory()->create();
        $service = Service::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 appointments
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $client->user_id,
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'scheduled',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $appointments = Appointment::with(['client', 'staff', 'service', 'location'])
            ->where('status', 'scheduled')
            ->where('appointment_date', '>=', now())
            ->where('appointment_date', '<=', now()->addMonth())
            ->orderBy('appointment_date')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(2.0, $executionTime, 'Complex appointment query should complete within 2 seconds for 1000 appointments');
        $this->assertLessThan(10, $queryCount, 'Complex appointment query should use less than 10 database queries');
        $this->assertGreaterThan(0, $appointments->count());
    }
}
