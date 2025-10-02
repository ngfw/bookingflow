<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ClientPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function client_creation_performance_test()
    {
        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Client creation should complete within 3 seconds for 100 clients');
        $this->assertEquals(100, Client::count());
    }

    /** @test */
    public function client_retrieval_performance_test()
    {
        // Create 1000 clients
        for ($i = 0; $i < 1000; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Retrieve all clients
        $clients = Client::orderBy('name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Client retrieval should complete within 2 seconds for 1000 clients');
        $this->assertEquals(1000, $clients->count());
    }

    /** @test */
    public function client_search_performance_test()
    {
        // Create 1000 clients
        for ($i = 0; $i < 1000; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Search clients by name
        $clients = Client::where('name', 'like', '%Client 1%')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Client search should complete within 1 second for 1000 clients');
        $this->assertGreaterThan(0, $clients->count());
    }

    /** @test */
    public function client_pagination_performance_test()
    {
        // Create 1000 clients
        for ($i = 0; $i < 1000; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Paginate clients
        $clients = Client::orderBy('name')->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Client pagination should complete within 1 second for 1000 clients');
        $this->assertEquals(50, $clients->count());
    }

    /** @test */
    public function client_update_performance_test()
    {
        // Create 100 clients
        $clients = [];
        for ($i = 0; $i < 100; $i++) {
            $clients[] = Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Update all clients
        foreach ($clients as $client) {
            $client->update([
                'city' => 'Updated City',
                'notes' => 'Updated via performance test',
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Client updates should complete within 3 seconds for 100 clients');
        $this->assertEquals(100, Client::where('city', 'Updated City')->count());
    }

    /** @test */
    public function client_bulk_update_performance_test()
    {
        // Create 100 clients
        $clientIds = [];
        for ($i = 0; $i < 100; $i++) {
            $client = Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
            $clientIds[] = $client->id;
        }

        $startTime = microtime(true);

        // Bulk update clients
        Client::whereIn('id', $clientIds)
            ->update([
                'city' => 'Bulk Updated City',
                'notes' => 'Bulk updated via performance test',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk client updates should complete within 1 second for 100 clients');
        $this->assertEquals(100, Client::where('city', 'Bulk Updated City')->count());
    }

    /** @test */
    public function client_deletion_performance_test()
    {
        // Create 100 clients
        $clients = [];
        for ($i = 0; $i < 100; $i++) {
            $clients[] = Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Soft delete all clients
        foreach ($clients as $client) {
            $client->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Client deletions should complete within 2 seconds for 100 clients');
        $this->assertEquals(0, Client::count());
        $this->assertEquals(100, Client::withTrashed()->count());
    }

    /** @test */
    public function client_bulk_deletion_performance_test()
    {
        // Create 100 clients
        $clientIds = [];
        for ($i = 0; $i < 100; $i++) {
            $client = Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
            $clientIds[] = $client->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete clients
        Client::whereIn('id', $clientIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk client deletions should complete within 1 second for 100 clients');
        $this->assertEquals(0, Client::count());
        $this->assertEquals(100, Client::withTrashed()->count());
    }

    /** @test */
    public function client_appointment_history_performance_test()
    {
        // Create 100 clients
        $clients = [];
        for ($i = 0; $i < 100; $i++) {
            $clients[] = Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        // Create 1000 appointments for these clients
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => $clients[$i % 100]->user_id,
                'staff_id' => 1,
                'service_id' => 1,
                'location_id' => 1,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'completed',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Get appointment history for all clients
        $clientsWithAppointments = Client::with(['appointments' => function ($query) {
            $query->orderBy('appointment_date', 'desc');
        }])->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Client appointment history retrieval should complete within 3 seconds for 100 clients with 1000 appointments');
        $this->assertEquals(100, $clientsWithAppointments->count());
    }

    /** @test */
    public function client_statistics_performance_test()
    {
        // Create 1000 clients with different cities
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'];
        for ($i = 0; $i < 1000; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => $cities[$i % 5],
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Calculate statistics
        $statistics = [
            'total_clients' => Client::count(),
            'clients_by_city' => Client::select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->get()
                ->pluck('count', 'city'),
            'new_clients_this_month' => Client::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Client statistics calculation should complete within 1 second for 1000 clients');
        $this->assertEquals(1000, $statistics['total_clients']);
        $this->assertEquals(5, $statistics['clients_by_city']->count());
    }

    /** @test */
    public function client_database_query_performance_test()
    {
        // Create 1000 clients
        for ($i = 0; $i < 1000; $i++) {
            Client::create([
                'name' => 'Client ' . $i,
                'email' => 'client' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'emergency_contact' => 'Emergency Contact ' . $i,
                'emergency_phone' => '+123456789' . $i,
                'allergies' => 'None',
                'notes' => 'Performance test client ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $clients = Client::where('city', 'New York')
            ->where('created_at', '>=', now()->subMonth())
            ->orderBy('name')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(1.0, $executionTime, 'Complex client query should complete within 1 second for 1000 clients');
        $this->assertLessThan(5, $queryCount, 'Complex client query should use less than 5 database queries');
        $this->assertGreaterThan(0, $clients->count());
    }
}
