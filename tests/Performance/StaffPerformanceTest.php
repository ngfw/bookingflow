<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class StaffPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function staff_creation_performance_test()
    {
        $location = Location::factory()->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Staff creation should complete within 3 seconds for 100 staff members');
        $this->assertEquals(100, Staff::count());
    }

    /** @test */
    public function staff_retrieval_performance_test()
    {
        $location = Location::factory()->create();

        // Create 1000 staff members
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Retrieve all staff members
        $staff = Staff::orderBy('name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Staff retrieval should complete within 2 seconds for 1000 staff members');
        $this->assertEquals(1000, $staff->count());
    }

    /** @test */
    public function staff_search_performance_test()
    {
        $location = Location::factory()->create();

        // Create 1000 staff members
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Search staff by name
        $staff = Staff::where('name', 'like', '%Staff 1%')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Staff search should complete within 1 second for 1000 staff members');
        $this->assertGreaterThan(0, $staff->count());
    }

    /** @test */
    public function staff_pagination_performance_test()
    {
        $location = Location::factory()->create();

        // Create 1000 staff members
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Paginate staff
        $staff = Staff::orderBy('name')->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Staff pagination should complete within 1 second for 1000 staff members');
        $this->assertEquals(50, $staff->count());
    }

    /** @test */
    public function staff_update_performance_test()
    {
        $location = Location::factory()->create();

        // Create 100 staff members
        $staff = [];
        for ($i = 0; $i < 100; $i++) {
            $staff[] = Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Update all staff members
        foreach ($staff as $staffMember) {
            $staffMember->update([
                'salary' => $staffMember->salary + 1000,
                'position' => 'Senior Stylist',
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Staff updates should complete within 3 seconds for 100 staff members');
        $this->assertEquals(100, Staff::where('position', 'Senior Stylist')->count());
    }

    /** @test */
    public function staff_bulk_update_performance_test()
    {
        $location = Location::factory()->create();

        // Create 100 staff members
        $staffIds = [];
        for ($i = 0; $i < 100; $i++) {
            $staff = Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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
            $staffIds[] = $staff->id;
        }

        $startTime = microtime(true);

        // Bulk update staff
        Staff::whereIn('id', $staffIds)
            ->update([
                'is_active' => false,
                'position' => 'Inactive Staff',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk staff updates should complete within 1 second for 100 staff members');
        $this->assertEquals(100, Staff::where('is_active', false)->count());
    }

    /** @test */
    public function staff_deletion_performance_test()
    {
        $location = Location::factory()->create();

        // Create 100 staff members
        $staff = [];
        for ($i = 0; $i < 100; $i++) {
            $staff[] = Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Soft delete all staff members
        foreach ($staff as $staffMember) {
            $staffMember->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Staff deletions should complete within 2 seconds for 100 staff members');
        $this->assertEquals(0, Staff::count());
        $this->assertEquals(100, Staff::withTrashed()->count());
    }

    /** @test */
    public function staff_bulk_deletion_performance_test()
    {
        $location = Location::factory()->create();

        // Create 100 staff members
        $staffIds = [];
        for ($i = 0; $i < 100; $i++) {
            $staff = Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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
            $staffIds[] = $staff->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete staff
        Staff::whereIn('id', $staffIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk staff deletions should complete within 1 second for 100 staff members');
        $this->assertEquals(0, Staff::count());
        $this->assertEquals(100, Staff::withTrashed()->count());
    }

    /** @test */
    public function staff_appointment_history_performance_test()
    {
        $location = Location::factory()->create();

        // Create 100 staff members
        $staff = [];
        for ($i = 0; $i < 100; $i++) {
            $staff[] = Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Create 1000 appointments for these staff members
        for ($i = 0; $i < 1000; $i++) {
            Appointment::create([
                'client_id' => 1,
                'staff_id' => $staff[$i % 100]->id,
                'service_id' => 1,
                'location_id' => $location->id,
                'appointment_date' => now()->addDays($i % 365)->format('Y-m-d H:i:s'),
                'duration' => 60,
                'price' => 50.00,
                'status' => 'completed',
                'notes' => 'Performance test appointment ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Get appointment history for all staff members
        $staffWithAppointments = Staff::with(['appointments' => function ($query) {
            $query->orderBy('appointment_date', 'desc');
        }])->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Staff appointment history retrieval should complete within 3 seconds for 100 staff members with 1000 appointments');
        $this->assertEquals(100, $staffWithAppointments->count());
    }

    /** @test */
    public function staff_statistics_performance_test()
    {
        $location = Location::factory()->create();

        // Create 1000 staff members with different positions
        $positions = ['Stylist', 'Senior Stylist', 'Manager', 'Assistant', 'Receptionist'];
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => $positions[$i % 5],
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
                'is_active' => $i % 2 === 0,
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

        // Calculate statistics
        $statistics = [
            'total_staff' => Staff::count(),
            'active_staff' => Staff::where('is_active', true)->count(),
            'inactive_staff' => Staff::where('is_active', false)->count(),
            'staff_by_position' => Staff::select('position', DB::raw('count(*) as count'))
                ->groupBy('position')
                ->get()
                ->pluck('count', 'position'),
            'average_salary' => Staff::avg('salary'),
            'total_commission_paid' => Staff::sum(DB::raw('salary * commission_rate / 100')),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Staff statistics calculation should complete within 1 second for 1000 staff members');
        $this->assertEquals(1000, $statistics['total_staff']);
        $this->assertEquals(500, $statistics['active_staff']);
        $this->assertEquals(500, $statistics['inactive_staff']);
        $this->assertEquals(5, $statistics['staff_by_position']->count());
    }

    /** @test */
    public function staff_database_query_performance_test()
    {
        $location = Location::factory()->create();

        // Create 1000 staff members
        for ($i = 0; $i < 1000; $i++) {
            Staff::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@example.com',
                'phone' => '+123456789' . $i,
                'position' => 'Stylist',
                'hire_date' => '2023-01-01',
                'salary' => 50000.00 + $i * 1000,
                'commission_rate' => 10.0,
                'location_id' => $location->id,
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

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $staff = Staff::where('is_active', true)
            ->where('salary', '>=', 60000)
            ->where('salary', '<=', 100000)
            ->orderBy('salary', 'desc')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(1.0, $executionTime, 'Complex staff query should complete within 1 second for 1000 staff members');
        $this->assertLessThan(5, $queryCount, 'Complex staff query should use less than 5 database queries');
        $this->assertGreaterThan(0, $staff->count());
    }
}
