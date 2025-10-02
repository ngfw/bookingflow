<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Service;
use App\Models\Category;
use App\Models\Appointment;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_service()
    {
        $serviceData = [
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
            'category_id' => 1,
            'is_active' => true,
        ];

        $service = Service::create($serviceData);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('Hair Cut', $service->name);
        $this->assertEquals(50.00, $service->price);
        $this->assertEquals(60, $service->duration);
        $this->assertTrue($service->is_active);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $service = Service::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $service->category);
        $this->assertEquals($category->id, $service->category->id);
    }

    /** @test */
    public function it_can_have_many_appointments()
    {
        $service = Service::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['service_id' => $service->id]);

        $this->assertCount(3, $service->appointments);
        $this->assertInstanceOf(Appointment::class, $service->appointments->first());
    }

    /** @test */
    public function it_can_belong_to_many_locations()
    {
        $service = Service::factory()->create();
        $location1 = Location::factory()->create();
        $location2 = Location::factory()->create();

        $service->locations()->attach([$location1->id, $location2->id]);

        $this->assertCount(2, $service->locations);
        $this->assertInstanceOf(Location::class, $service->locations->first());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $service = Service::factory()->create([
            'price' => 75.50,
            'duration' => 90,
            'is_active' => true,
            'requires_consultation' => false,
            'is_popular' => true,
        ]);

        $this->assertIsFloat($service->price);
        $this->assertIsInt($service->duration);
        $this->assertIsBool($service->is_active);
        $this->assertIsBool($service->requires_consultation);
        $this->assertIsBool($service->is_popular);
    }

    /** @test */
    public function it_can_scope_active_services()
    {
        Service::factory()->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);

        $activeServices = Service::where('is_active', true)->get();

        $this->assertCount(1, $activeServices);
        $this->assertTrue($activeServices->first()->is_active);
    }

    /** @test */
    public function it_can_scope_popular_services()
    {
        Service::factory()->create(['is_popular' => true]);
        Service::factory()->create(['is_popular' => false]);

        $popularServices = Service::where('is_popular', true)->get();

        $this->assertCount(1, $popularServices);
        $this->assertTrue($popularServices->first()->is_popular);
    }

    /** @test */
    public function it_can_scope_services_by_category()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Service::factory()->create(['category_id' => $category1->id]);
        Service::factory()->create(['category_id' => $category2->id]);

        $category1Services = Service::where('category_id', $category1->id)->get();

        $this->assertCount(1, $category1Services);
        $this->assertEquals($category1->id, $category1Services->first()->category_id);
    }

    /** @test */
    public function it_can_scope_services_by_price_range()
    {
        Service::factory()->create(['price' => 30.00]);
        Service::factory()->create(['price' => 50.00]);
        Service::factory()->create(['price' => 80.00]);

        $affordableServices = Service::whereBetween('price', [20.00, 60.00])->get();

        $this->assertCount(2, $affordableServices);
    }

    /** @test */
    public function it_can_scope_services_by_duration()
    {
        Service::factory()->create(['duration' => 30]);
        Service::factory()->create(['duration' => 60]);
        Service::factory()->create(['duration' => 90]);

        $quickServices = Service::where('duration', '<=', 45)->get();

        $this->assertCount(1, $quickServices);
        $this->assertEquals(30, $quickServices->first()->duration);
    }

    /** @test */
    public function it_can_scope_services_requiring_consultation()
    {
        Service::factory()->create(['requires_consultation' => true]);
        Service::factory()->create(['requires_consultation' => false]);

        $consultationServices = Service::where('requires_consultation', true)->get();

        $this->assertCount(1, $consultationServices);
        $this->assertTrue($consultationServices->first()->requires_consultation);
    }

    /** @test */
    public function it_can_get_service_duration_in_hours()
    {
        $service = Service::factory()->create(['duration' => 120]);

        $this->assertEquals(2, $service->duration_in_hours);
    }

    /** @test */
    public function it_can_get_service_duration_in_minutes()
    {
        $service = Service::factory()->create(['duration' => 90]);

        $this->assertEquals(90, $service->duration_in_minutes);
    }

    /** @test */
    public function it_can_get_formatted_price()
    {
        $service = Service::factory()->create(['price' => 75.50]);

        $this->assertEquals('$75.50', $service->formatted_price);
    }

    /** @test */
    public function it_can_get_service_status_display()
    {
        $activeService = Service::factory()->create(['is_active' => true]);
        $inactiveService = Service::factory()->create(['is_active' => false]);

        $this->assertEquals('Active', $activeService->status_display);
        $this->assertEquals('Inactive', $inactiveService->status_display);
    }

    /** @test */
    public function it_can_get_service_status_color()
    {
        $activeService = Service::factory()->create(['is_active' => true]);
        $inactiveService = Service::factory()->create(['is_active' => false]);

        $this->assertEquals('green', $activeService->status_color);
        $this->assertEquals('red', $inactiveService->status_color);
    }

    /** @test */
    public function it_can_get_appointments_count()
    {
        $service = Service::factory()->create();
        Appointment::factory()->count(3)->create(['service_id' => $service->id]);

        $this->assertEquals(3, $service->appointments_count);
    }

    /** @test */
    public function it_can_get_revenue()
    {
        $service = Service::factory()->create(['price' => 50.00]);
        Appointment::factory()->count(2)->create([
            'service_id' => $service->id,
            'status' => 'completed',
        ]);

        $this->assertEquals(100.00, $service->revenue);
    }

    /** @test */
    public function it_can_get_average_rating()
    {
        $service = Service::factory()->create();
        
        // Create appointments with ratings
        Appointment::factory()->create([
            'service_id' => $service->id,
            'rating' => 5,
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'rating' => 4,
        ]);

        $this->assertEquals(4.5, $service->average_rating);
    }

    /** @test */
    public function it_can_get_total_reviews()
    {
        $service = Service::factory()->create();
        
        Appointment::factory()->create([
            'service_id' => $service->id,
            'rating' => 5,
            'review' => 'Great service!',
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'rating' => 4,
            'review' => 'Good service',
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'rating' => null,
        ]);

        $this->assertEquals(2, $service->total_reviews);
    }

    /** @test */
    public function it_can_check_if_service_is_available()
    {
        $availableService = Service::factory()->create(['is_active' => true]);
        $unavailableService = Service::factory()->create(['is_active' => false]);

        $this->assertTrue($availableService->is_available);
        $this->assertFalse($unavailableService->is_available);
    }

    /** @test */
    public function it_can_check_if_service_is_popular()
    {
        $popularService = Service::factory()->create(['is_popular' => true]);
        $regularService = Service::factory()->create(['is_popular' => false]);

        $this->assertTrue($popularService->is_popular);
        $this->assertFalse($regularService->is_popular);
    }

    /** @test */
    public function it_can_activate_service()
    {
        $service = Service::factory()->create(['is_active' => false]);

        $service->activate();

        $this->assertTrue($service->is_active);
    }

    /** @test */
    public function it_can_deactivate_service()
    {
        $service = Service::factory()->create(['is_active' => true]);

        $service->deactivate();

        $this->assertFalse($service->is_active);
    }

    /** @test */
    public function it_can_mark_as_popular()
    {
        $service = Service::factory()->create(['is_popular' => false]);

        $service->markAsPopular();

        $this->assertTrue($service->is_popular);
    }

    /** @test */
    public function it_can_unmark_as_popular()
    {
        $service = Service::factory()->create(['is_popular' => true]);

        $service->unmarkAsPopular();

        $this->assertFalse($service->is_popular);
    }

    /** @test */
    public function it_can_update_price()
    {
        $service = Service::factory()->create(['price' => 50.00]);

        $service->updatePrice(75.00, 'Price increase due to market conditions');

        $this->assertEquals(75.00, $service->price);
        $this->assertEquals('Price increase due to market conditions', $service->price_change_reason);
    }

    /** @test */
    public function it_can_update_duration()
    {
        $service = Service::factory()->create(['duration' => 60]);

        $service->updateDuration(90, 'Extended duration for better service quality');

        $this->assertEquals(90, $service->duration);
        $this->assertEquals('Extended duration for better service quality', $service->duration_change_reason);
    }

    /** @test */
    public function it_can_get_service_statistics()
    {
        $service = Service::factory()->create(['price' => 50.00]);
        
        // Create appointments with different statuses
        Appointment::factory()->create([
            'service_id' => $service->id,
            'status' => 'completed',
            'rating' => 5,
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'status' => 'completed',
            'rating' => 4,
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'status' => 'cancelled',
        ]);

        $stats = $service->getStatistics();

        $this->assertEquals(3, $stats['total_appointments']);
        $this->assertEquals(2, $stats['completed_appointments']);
        $this->assertEquals(1, $stats['cancelled_appointments']);
        $this->assertEquals(100.00, $stats['total_revenue']);
        $this->assertEquals(4.5, $stats['average_rating']);
        $this->assertEquals(2, $stats['total_reviews']);
    }

    /** @test */
    public function it_can_get_service_performance_metrics()
    {
        $service = Service::factory()->create();
        
        // Create appointments for different time periods
        Appointment::factory()->create([
            'service_id' => $service->id,
            'status' => 'completed',
            'created_at' => now()->subMonth(),
        ]);
        Appointment::factory()->create([
            'service_id' => $service->id,
            'status' => 'completed',
            'created_at' => now()->subWeek(),
        ]);

        $metrics = $service->getPerformanceMetrics();

        $this->assertArrayHasKey('monthly_appointments', $metrics);
        $this->assertArrayHasKey('weekly_appointments', $metrics);
        $this->assertArrayHasKey('completion_rate', $metrics);
        $this->assertArrayHasKey('cancellation_rate', $metrics);
    }

    /** @test */
    public function it_can_validate_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Service::create([]);
    }

    /** @test */
    public function it_can_validate_price_is_positive()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Service::factory()->create(['price' => -10.00]);
    }

    /** @test */
    public function it_can_validate_duration_is_positive()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Service::factory()->create(['duration' => -30]);
    }

    /** @test */
    public function it_can_soft_delete()
    {
        $service = Service::factory()->create();
        $serviceId = $service->id;

        $service->delete();

        $this->assertSoftDeleted('services', ['id' => $serviceId]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_service()
    {
        $service = Service::factory()->create();
        $service->delete();

        $this->assertSoftDeleted('services', ['id' => $service->id]);

        $service->restore();

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_force_delete()
    {
        $service = Service::factory()->create();
        $serviceId = $service->id;

        $service->forceDelete();

        $this->assertDatabaseMissing('services', ['id' => $serviceId]);
    }

    /** @test */
    public function it_can_get_service_search_results()
    {
        Service::factory()->create(['name' => 'Hair Cut', 'description' => 'Professional hair cutting']);
        Service::factory()->create(['name' => 'Hair Color', 'description' => 'Hair coloring service']);
        Service::factory()->create(['name' => 'Facial', 'description' => 'Skin care treatment']);

        $hairServices = Service::where('name', 'like', '%Hair%')->get();

        $this->assertCount(2, $hairServices);
    }

    /** @test */
    public function it_can_get_services_by_popularity()
    {
        $popularService = Service::factory()->create(['is_popular' => true]);
        $regularService = Service::factory()->create(['is_popular' => false]);

        $popularServices = Service::where('is_popular', true)->get();

        $this->assertCount(1, $popularServices);
        $this->assertEquals($popularService->id, $popularServices->first()->id);
    }

    /** @test */
    public function it_can_get_services_by_price()
    {
        Service::factory()->create(['price' => 30.00]);
        Service::factory()->create(['price' => 50.00]);
        Service::factory()->create(['price' => 80.00]);

        $expensiveServices = Service::where('price', '>', 60.00)->get();

        $this->assertCount(1, $expensiveServices);
        $this->assertEquals(80.00, $expensiveServices->first()->price);
    }

    /** @test */
    public function it_can_get_services_by_duration()
    {
        Service::factory()->create(['duration' => 30]);
        Service::factory()->create(['duration' => 60]);
        Service::factory()->create(['duration' => 90]);

        $longServices = Service::where('duration', '>', 60)->get();

        $this->assertCount(1, $longServices);
        $this->assertEquals(90, $longServices->first()->duration);
    }
}
