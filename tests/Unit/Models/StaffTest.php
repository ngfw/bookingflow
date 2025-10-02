<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Staff;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_staff()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $location = Location::factory()->create();
        
        $staffData = [
            'user_id' => $user->id,
            'location_id' => $location->id,
            'employee_id' => 'EMP001',
            'hire_date' => now(),
            'position' => 'Senior Stylist',
            'hourly_rate' => 25.00,
            'commission_rate' => 0.10,
            'is_active' => true,
        ];

        $staff = Staff::create($staffData);

        $this->assertInstanceOf(Staff::class, $staff);
        $this->assertEquals($user->id, $staff->user_id);
        $this->assertEquals('EMP001', $staff->employee_id);
        $this->assertEquals('Senior Stylist', $staff->position);
        $this->assertEquals(25.00, $staff->hourly_rate);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $staff->user);
        $this->assertEquals($user->id, $staff->user->id);
    }

    /** @test */
    public function it_belongs_to_a_location()
    {
        $location = Location::factory()->create();
        $staff = Staff::factory()->create(['location_id' => $location->id]);

        $this->assertInstanceOf(Location::class, $staff->location);
        $this->assertEquals($location->id, $staff->location->id);
    }

    /** @test */
    public function it_can_have_many_appointments()
    {
        $staff = Staff::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['staff_id' => $staff->id]);

        $this->assertCount(3, $staff->appointments);
        $this->assertInstanceOf(Appointment::class, $staff->appointments->first());
    }

    /** @test */
    public function it_can_have_many_services()
    {
        $staff = Staff::factory()->create();
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();

        $staff->services()->attach([$service1->id, $service2->id]);

        $this->assertCount(2, $staff->services);
        $this->assertInstanceOf(Service::class, $staff->services->first());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $staff = Staff::factory()->create([
            'hire_date' => '2024-01-15',
            'hourly_rate' => 25.50,
            'commission_rate' => 0.15,
            'is_active' => true,
            'skills' => ['hair_cutting', 'coloring', 'styling'],
            'certifications' => ['cosmetology_license', 'color_specialist'],
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $staff->hire_date);
        $this->assertIsFloat($staff->hourly_rate);
        $this->assertIsFloat($staff->commission_rate);
        $this->assertIsBool($staff->is_active);
        $this->assertIsArray($staff->skills);
        $this->assertIsArray($staff->certifications);
    }

    /** @test */
    public function it_can_scope_active_staff()
    {
        Staff::factory()->create(['is_active' => true]);
        Staff::factory()->create(['is_active' => false]);

        $activeStaff = Staff::where('is_active', true)->get();

        $this->assertCount(1, $activeStaff);
        $this->assertTrue($activeStaff->first()->is_active);
    }

    /** @test */
    public function it_can_scope_staff_by_location()
    {
        $location1 = Location::factory()->create();
        $location2 = Location::factory()->create();

        Staff::factory()->create(['location_id' => $location1->id]);
        Staff::factory()->create(['location_id' => $location2->id]);

        $location1Staff = Staff::where('location_id', $location1->id)->get();

        $this->assertCount(1, $location1Staff);
        $this->assertEquals($location1->id, $location1Staff->first()->location_id);
    }

    /** @test */
    public function it_can_scope_staff_by_position()
    {
        Staff::factory()->create(['position' => 'Senior Stylist']);
        Staff::factory()->create(['position' => 'Junior Stylist']);

        $seniorStaff = Staff::where('position', 'Senior Stylist')->get();

        $this->assertCount(1, $seniorStaff);
        $this->assertEquals('Senior Stylist', $seniorStaff->first()->position);
    }

    /** @test */
    public function it_can_get_staff_name()
    {
        $user = User::factory()->create(['name' => 'Jane Smith']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('Jane Smith', $staff->name);
    }

    /** @test */
    public function it_can_get_staff_email()
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('jane@example.com', $staff->email);
    }

    /** @test */
    public function it_can_get_staff_phone()
    {
        $user = User::factory()->create(['phone' => '1234567890']);
        $staff = Staff::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('1234567890', $staff->phone);
    }

    /** @test */
    public function it_can_get_total_appointments()
    {
        $staff = Staff::factory()->create();
        Appointment::factory()->count(3)->create(['staff_id' => $staff->id]);

        $this->assertEquals(3, $staff->total_appointments);
    }

    /** @test */
    public function it_can_get_completed_appointments()
    {
        $staff = Staff::factory()->create();
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'scheduled',
        ]);

        $this->assertEquals(1, $staff->completed_appointments);
    }

    /** @test */
    public function it_can_get_cancelled_appointments()
    {
        $staff = Staff::factory()->create();
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'cancelled',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'scheduled',
        ]);

        $this->assertEquals(1, $staff->cancelled_appointments);
    }

    /** @test */
    public function it_can_get_total_revenue()
    {
        $staff = Staff::factory()->create();
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'price' => 50.00,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'price' => 75.00,
            'status' => 'completed',
        ]);

        $this->assertEquals(125.00, $staff->total_revenue);
    }

    /** @test */
    public function it_can_get_commission_earned()
    {
        $staff = Staff::factory()->create(['commission_rate' => 0.10]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'price' => 100.00,
            'status' => 'completed',
        ]);

        $this->assertEquals(10.00, $staff->commission_earned);
    }

    /** @test */
    public function it_can_get_hours_worked()
    {
        $staff = Staff::factory()->create();
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'duration' => 60,
            'status' => 'completed',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'duration' => 90,
            'status' => 'completed',
        ]);

        $this->assertEquals(2.5, $staff->hours_worked);
    }

    /** @test */
    public function it_can_get_average_rating()
    {
        $staff = Staff::factory()->create();
        
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'rating' => 5,
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'rating' => 4,
        ]);

        $this->assertEquals(4.5, $staff->average_rating);
    }

    /** @test */
    public function it_can_get_total_reviews()
    {
        $staff = Staff::factory()->create();
        
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'rating' => 5,
            'review' => 'Great service!',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'rating' => 4,
            'review' => 'Good service',
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'rating' => null,
        ]);

        $this->assertEquals(2, $staff->total_reviews);
    }

    /** @test */
    public function it_can_get_employment_duration()
    {
        $staff = Staff::factory()->create(['hire_date' => now()->subYear()]);

        $this->assertEquals(1, $staff->employment_duration_years);
    }

    /** @test */
    public function it_can_check_if_staff_has_skill()
    {
        $staff = Staff::factory()->create(['skills' => ['hair_cutting', 'coloring', 'styling']]);

        $this->assertTrue($staff->hasSkill('hair_cutting'));
        $this->assertTrue($staff->hasSkill('coloring'));
        $this->assertFalse($staff->hasSkill('massage'));
    }

    /** @test */
    public function it_can_check_if_staff_has_certification()
    {
        $staff = Staff::factory()->create(['certifications' => ['cosmetology_license', 'color_specialist']]);

        $this->assertTrue($staff->hasCertification('cosmetology_license'));
        $this->assertTrue($staff->hasCertification('color_specialist'));
        $this->assertFalse($staff->hasCertification('massage_therapy'));
    }

    /** @test */
    public function it_can_add_skill()
    {
        $staff = Staff::factory()->create(['skills' => ['hair_cutting']]);

        $staff->addSkill('coloring');

        $this->assertContains('hair_cutting', $staff->skills);
        $this->assertContains('coloring', $staff->skills);
    }

    /** @test */
    public function it_can_remove_skill()
    {
        $staff = Staff::factory()->create(['skills' => ['hair_cutting', 'coloring']]);

        $staff->removeSkill('hair_cutting');

        $this->assertNotContains('hair_cutting', $staff->skills);
        $this->assertContains('coloring', $staff->skills);
    }

    /** @test */
    public function it_can_add_certification()
    {
        $staff = Staff::factory()->create(['certifications' => ['cosmetology_license']]);

        $staff->addCertification('color_specialist');

        $this->assertContains('cosmetology_license', $staff->certifications);
        $this->assertContains('color_specialist', $staff->certifications);
    }

    /** @test */
    public function it_can_remove_certification()
    {
        $staff = Staff::factory()->create(['certifications' => ['cosmetology_license', 'color_specialist']]);

        $staff->removeCertification('cosmetology_license');

        $this->assertNotContains('cosmetology_license', $staff->certifications);
        $this->assertContains('color_specialist', $staff->certifications);
    }

    /** @test */
    public function it_can_activate_staff()
    {
        $staff = Staff::factory()->create(['is_active' => false]);

        $staff->activate();

        $this->assertTrue($staff->is_active);
    }

    /** @test */
    public function it_can_deactivate_staff()
    {
        $staff = Staff::factory()->create(['is_active' => true]);

        $staff->deactivate();

        $this->assertFalse($staff->is_active);
    }

    /** @test */
    public function it_can_update_hourly_rate()
    {
        $staff = Staff::factory()->create(['hourly_rate' => 20.00]);

        $staff->updateHourlyRate(25.00, 'Performance review increase');

        $this->assertEquals(25.00, $staff->hourly_rate);
        $this->assertEquals('Performance review increase', $staff->rate_change_reason);
    }

    /** @test */
    public function it_can_update_commission_rate()
    {
        $staff = Staff::factory()->create(['commission_rate' => 0.10]);

        $staff->updateCommissionRate(0.15, 'Increased commission for senior staff');

        $this->assertEquals(0.15, $staff->commission_rate);
        $this->assertEquals('Increased commission for senior staff', $staff->commission_change_reason);
    }

    /** @test */
    public function it_can_get_staff_performance_metrics()
    {
        $staff = Staff::factory()->create();
        
        // Create appointments for different time periods
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'completed',
            'created_at' => now()->subMonth(),
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'completed',
            'created_at' => now()->subWeek(),
        ]);

        $metrics = $staff->getPerformanceMetrics();

        $this->assertArrayHasKey('monthly_appointments', $metrics);
        $this->assertArrayHasKey('weekly_appointments', $metrics);
        $this->assertArrayHasKey('completion_rate', $metrics);
        $this->assertArrayHasKey('cancellation_rate', $metrics);
        $this->assertArrayHasKey('average_rating', $metrics);
    }

    /** @test */
    public function it_can_get_staff_statistics()
    {
        $staff = Staff::factory()->create(['commission_rate' => 0.10]);
        
        // Create appointments with different statuses
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'completed',
            'price' => 100.00,
            'rating' => 5,
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'completed',
            'price' => 50.00,
            'rating' => 4,
        ]);
        Appointment::factory()->create([
            'staff_id' => $staff->id,
            'status' => 'cancelled',
        ]);

        $stats = $staff->getStatistics();

        $this->assertEquals(3, $stats['total_appointments']);
        $this->assertEquals(2, $stats['completed_appointments']);
        $this->assertEquals(1, $stats['cancelled_appointments']);
        $this->assertEquals(150.00, $stats['total_revenue']);
        $this->assertEquals(15.00, $stats['commission_earned']);
        $this->assertEquals(4.5, $stats['average_rating']);
        $this->assertEquals(2, $stats['total_reviews']);
    }

    /** @test */
    public function it_can_get_staff_schedule()
    {
        $staff = Staff::factory()->create();
        
        $schedule = $staff->getSchedule(now()->startOfWeek(), now()->endOfWeek());

        $this->assertIsArray($schedule);
        $this->assertArrayHasKey('monday', $schedule);
        $this->assertArrayHasKey('tuesday', $schedule);
        $this->assertArrayHasKey('wednesday', $schedule);
        $this->assertArrayHasKey('thursday', $schedule);
        $this->assertArrayHasKey('friday', $schedule);
        $this->assertArrayHasKey('saturday', $schedule);
        $this->assertArrayHasKey('sunday', $schedule);
    }

    /** @test */
    public function it_can_get_staff_availability()
    {
        $staff = Staff::factory()->create();
        
        $availability = $staff->getAvailability(now()->addDay());

        $this->assertIsArray($availability);
        $this->assertArrayHasKey('available_slots', $availability);
        $this->assertArrayHasKey('booked_slots', $availability);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Staff::create([]);
    }

    /** @test */
    public function it_can_validate_hourly_rate_is_positive()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Staff::factory()->create(['hourly_rate' => -10.00]);
    }

    /** @test */
    public function it_can_validate_commission_rate_is_positive()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Staff::factory()->create(['commission_rate' => -0.10]);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $staff = Staff::factory()->create();
        $staffId = $staff->id;

        $staff->delete();

        $this->assertSoftDeleted('staff', ['id' => $staffId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_staff()
    {
        $staff = Staff::factory()->create();
        $staff->delete();

        $this->assertSoftDeleted('staff', ['id' => $staff->id]);

        $staff->restore();

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $staff = Staff::factory()->create();
        $staffId = $staff->id;

        $staff->forceDelete();

        $this->assertDatabaseMissing('staff', ['id' => $staffId]);
    }

    /** @test */
    public function it_can_get_staff_search_results()
    {
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        
        Staff::factory()->create(['user_id' => $user1->id]);
        Staff::factory()->create(['user_id' => $user2->id]);

        $johnStaff = Staff::whereHas('user', function ($query) {
            $query->where('name', 'like', '%John%');
        })->get();

        $this->assertCount(1, $johnStaff);
    }

    /** @test */
    public function it_can_get_staff_by_position()
    {
        Staff::factory()->create(['position' => 'Senior Stylist']);
        Staff::factory()->create(['position' => 'Junior Stylist']);

        $seniorStaff = Staff::where('position', 'Senior Stylist')->get();

        $this->assertCount(1, $seniorStaff);
        $this->assertEquals('Senior Stylist', $seniorStaff->first()->position);
    }

    /** @test */
    public function it_can_get_staff_by_skills()
    {
        Staff::factory()->create(['skills' => ['hair_cutting', 'coloring']]);
        Staff::factory()->create(['skills' => ['styling', 'blow_dry']]);

        $hairCuttingStaff = Staff::whereJsonContains('skills', 'hair_cutting')->get();

        $this->assertCount(1, $hairCuttingStaff);
    }

    /** @test */
    public function it_can_get_staff_by_certifications()
    {
        Staff::factory()->create(['certifications' => ['cosmetology_license']]);
        Staff::factory()->create(['certifications' => ['color_specialist']]);

        $licensedStaff = Staff::whereJsonContains('certifications', 'cosmetology_license')->get();

        $this->assertCount(1, $licensedStaff);
    }
}
