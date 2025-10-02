<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Location;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function admin_can_create_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $this->actingAs($admin);

        $serviceData = [
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'is_active' => true,
            'is_popular' => false,
            'requires_consultation' => false,
        ];

        $response = $this->post('/admin/services', $serviceData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'name' => 'Hair Cut',
            'description' => 'Professional hair cutting service',
            'price' => 50.00,
            'duration' => 60,
            'category_id' => $category->id,
            'location_id' => $location->id,
        ]);
    }

    /** @test */
    public function admin_can_view_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $services = Service::factory()->count(3)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.index');
    }

    /** @test */
    public function admin_can_view_service_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.show');
    }

    /** @test */
    public function admin_can_update_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $updateData = [
            'name' => 'Updated Hair Cut',
            'price' => 75.00,
            'duration' => 90,
            'description' => 'Updated description',
        ];

        $response = $this->put('/admin/services/' . $service->id, $updateData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Hair Cut',
            'price' => 75.00,
            'duration' => 90,
        ]);
    }

    /** @test */
    public function admin_can_delete_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete('/admin/services/' . $service->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    /** @test */
    public function admin_can_activate_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_active' => false]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/activate');

        $response->assertStatus(302);
        $this->assertTrue($service->fresh()->is_active);
    }

    /** @test */
    public function admin_can_deactivate_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_active' => true]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/deactivate');

        $response->assertStatus(302);
        $this->assertFalse($service->fresh()->is_active);
    }

    /** @test */
    public function admin_can_mark_service_as_popular()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_popular' => false]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/mark-popular');

        $response->assertStatus(302);
        $this->assertTrue($service->fresh()->is_popular);
    }

    /** @test */
    public function admin_can_unmark_service_as_popular()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_popular' => true]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/unmark-popular');

        $response->assertStatus(302);
        $this->assertFalse($service->fresh()->is_popular);
    }

    /** @test */
    public function admin_can_update_service_price()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['price' => 50.00]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/price', [
            'price' => 75.00,
            'reason' => 'Price increase due to market conditions',
        ]);

        $response->assertStatus(302);
        $this->assertEquals(75.00, $service->fresh()->price);
        $this->assertEquals('Price increase due to market conditions', $service->fresh()->price_change_reason);
    }

    /** @test */
    public function admin_can_update_service_duration()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['duration' => 60]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/duration', [
            'duration' => 90,
            'reason' => 'Extended duration for better service quality',
        ]);

        $response->assertStatus(302);
        $this->assertEquals(90, $service->fresh()->duration);
        $this->assertEquals('Extended duration for better service quality', $service->fresh()->duration_change_reason);
    }

    /** @test */
    public function admin_can_view_service_appointments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $appointments = Appointment::factory()->count(3)->create(['service_id' => $service->id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.appointments');
    }

    /** @test */
    public function admin_can_view_service_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/statistics');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.statistics');
    }

    /** @test */
    public function admin_can_filter_services_by_category()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $service1 = Service::factory()->create(['category_id' => $category1->id]);
        $service2 = Service::factory()->create(['category_id' => $category2->id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services?category_id=' . $category1->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.index');
    }

    /** @test */
    public function admin_can_filter_services_by_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activeService = Service::factory()->create(['is_active' => true]);
        $inactiveService = Service::factory()->create(['is_active' => false]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services?status=active');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.index');
    }

    /** @test */
    public function admin_can_filter_services_by_popularity()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $popularService = Service::factory()->create(['is_popular' => true]);
        $regularService = Service::factory()->create(['is_popular' => false]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services?popular=true');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.index');
    }

    /** @test */
    public function admin_can_search_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service1 = Service::factory()->create(['name' => 'Hair Cut']);
        $service2 = Service::factory()->create(['name' => 'Hair Color']);
        $this->actingAs($admin);

        $response = $this->get('/admin/services?search=Hair');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.index');
    }

    /** @test */
    public function admin_can_export_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Service::factory()->count(3)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_import_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $this->actingAs($admin);

        $csvContent = "name,description,price,duration,category_id,location_id\nHair Cut,Professional hair cutting,50,60,{$category->id},{$location->id}\nHair Color,Professional hair coloring,75,90,{$category->id},{$location->id}";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('services.csv', $csvContent);

        $response = $this->post('/admin/services/import', [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'name' => 'Hair Cut',
            'price' => 50.00,
        ]);
    }

    /** @test */
    public function admin_can_bulk_update_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service1 = Service::factory()->create(['is_active' => false]);
        $service2 = Service::factory()->create(['is_active' => false]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/bulk-update', [
            'service_ids' => [$service1->id, $service2->id],
            'is_active' => true,
        ]);

        $response->assertStatus(302);
        $this->assertTrue($service1->fresh()->is_active);
        $this->assertTrue($service2->fresh()->is_active);
    }

    /** @test */
    public function admin_can_bulk_delete_services()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete('/admin/services/bulk-delete', [
            'service_ids' => [$service1->id, $service2->id],
        ]);

        $response->assertStatus(302);
        $this->assertSoftDeleted('services', ['id' => $service1->id]);
        $this->assertSoftDeleted('services', ['id' => $service2->id]);
    }

    /** @test */
    public function admin_can_view_service_performance_metrics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/performance');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.performance');
    }

    /** @test */
    public function admin_can_view_service_revenue()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/revenue');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.revenue');
    }

    /** @test */
    public function admin_can_view_service_ratings()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/ratings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.ratings');
    }

    /** @test */
    public function admin_can_view_service_reviews()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/reviews');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.reviews');
    }

    /** @test */
    public function admin_can_duplicate_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->post('/admin/services/' . $service->id . '/duplicate');

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'name' => $service->name . ' (Copy)',
            'category_id' => $service->category_id,
            'location_id' => $service->location_id,
        ]);
    }

    /** @test */
    public function admin_can_archive_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create(['is_active' => true]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/archive');

        $response->assertStatus(302);
        $this->assertFalse($service->fresh()->is_active);
        $this->assertNotNull($service->fresh()->archived_at);
    }

    /** @test */
    public function admin_can_restore_archived_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create([
            'is_active' => false,
            'archived_at' => now(),
        ]);
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/restore');

        $response->assertStatus(302);
        $this->assertTrue($service->fresh()->is_active);
        $this->assertNull($service->fresh()->archived_at);
    }

    /** @test */
    public function admin_can_view_service_categories()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $services = Service::factory()->count(3)->create(['category_id' => $category->id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services/categories/' . $category->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.category');
    }

    /** @test */
    public function admin_can_view_service_locations()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location = Location::factory()->create();
        $services = Service::factory()->count(3)->create(['location_id' => $location->id]);
        $this->actingAs($admin);

        $response = $this->get('/admin/services/locations/' . $location->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.location');
    }

    /** @test */
    public function admin_can_view_service_pricing_history()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/pricing-history');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.pricing-history');
    }

    /** @test */
    public function admin_can_view_service_duration_history()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/duration-history');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.duration-history');
    }

    /** @test */
    public function admin_can_view_service_availability()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/availability');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.availability');
    }

    /** @test */
    public function admin_can_update_service_availability()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->put('/admin/services/' . $service->id . '/availability', [
            'monday' => ['open' => '09:00', 'close' => '18:00'],
            'tuesday' => ['open' => '09:00', 'close' => '18:00'],
            'wednesday' => ['open' => '09:00', 'close' => '18:00'],
            'thursday' => ['open' => '09:00', 'close' => '18:00'],
            'friday' => ['open' => '09:00', 'close' => '18:00'],
            'saturday' => ['open' => '10:00', 'close' => '16:00'],
            'sunday' => ['open' => 'closed', 'close' => 'closed'],
        ]);

        $response->assertStatus(302);
        $this->assertIsArray($service->fresh()->availability);
    }

    /** @test */
    public function admin_can_view_service_staff_assignments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/staff');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.staff');
    }

    /** @test */
    public function admin_can_assign_staff_to_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $staff = \App\Models\Staff::factory()->create();
        $this->actingAs($admin);

        $response = $this->post('/admin/services/' . $service->id . '/staff', [
            'staff_id' => $staff->id,
        ]);

        $response->assertStatus(302);
        $this->assertTrue($service->fresh()->staff->contains($staff));
    }

    /** @test */
    public function admin_can_remove_staff_from_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $staff = \App\Models\Staff::factory()->create();
        $service->staff()->attach($staff->id);
        $this->actingAs($admin);

        $response = $this->delete('/admin/services/' . $service->id . '/staff/' . $staff->id);

        $response->assertStatus(302);
        $this->assertFalse($service->fresh()->staff->contains($staff));
    }

    /** @test */
    public function admin_can_view_service_equipment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/equipment');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.equipment');
    }

    /** @test */
    public function admin_can_add_equipment_to_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->post('/admin/services/' . $service->id . '/equipment', [
            'equipment_name' => 'Hair Dryer',
            'quantity' => 2,
            'notes' => 'Required for styling',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('service_equipment', [
            'service_id' => $service->id,
            'equipment_name' => 'Hair Dryer',
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function admin_can_remove_equipment_from_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $equipment = \App\Models\ServiceEquipment::factory()->create(['service_id' => $service->id]);
        $this->actingAs($admin);

        $response = $this->delete('/admin/services/' . $service->id . '/equipment/' . $equipment->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('service_equipment', ['id' => $equipment->id]);
    }

    /** @test */
    public function admin_can_view_service_supplies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/services/' . $service->id . '/supplies');

        $response->assertStatus(200);
        $response->assertViewIs('admin.services.supplies');
    }

    /** @test */
    public function admin_can_add_supplies_to_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $this->actingAs($admin);

        $response = $this->post('/admin/services/' . $service->id . '/supplies', [
            'supply_name' => 'Shampoo',
            'quantity' => 1,
            'unit' => 'bottle',
            'notes' => 'Required for washing',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('service_supplies', [
            'service_id' => $service->id,
            'supply_name' => 'Shampoo',
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function admin_can_remove_supplies_from_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::factory()->create();
        $supply = \App\Models\ServiceSupply::factory()->create(['service_id' => $service->id]);
        $this->actingAs($admin);

        $response = $this->delete('/admin/services/' . $service->id . '/supplies/' . $supply->id);

        $response->assertStatus(302);
        $this->assertSoftDeleted('service_supplies', ['id' => $supply->id]);
    }
}
