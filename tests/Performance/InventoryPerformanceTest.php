<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class InventoryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function inventory_creation_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Inventory creation should complete within 3 seconds for 100 items');
        $this->assertEquals(100, Inventory::count());
    }

    /** @test */
    public function inventory_retrieval_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Retrieve all inventory items
        $inventory = Inventory::orderBy('name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Inventory retrieval should complete within 2 seconds for 1000 items');
        $this->assertEquals(1000, $inventory->count());
    }

    /** @test */
    public function inventory_search_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Search inventory by name
        $inventory = Inventory::where('name', 'like', '%Item 1%')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Inventory search should complete within 1 second for 1000 items');
        $this->assertGreaterThan(0, $inventory->count());
    }

    /** @test */
    public function inventory_pagination_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Paginate inventory
        $inventory = Inventory::orderBy('name')->paginate(50);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Inventory pagination should complete within 1 second for 1000 items');
        $this->assertEquals(50, $inventory->count());
    }

    /** @test */
    public function inventory_update_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 100 inventory items
        $inventory = [];
        for ($i = 0; $i < 100; $i++) {
            $inventory[] = Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Update all inventory items
        foreach ($inventory as $item) {
            $item->update([
                'quantity' => $item->quantity + 10,
                'unit_price' => $item->unit_price + 5,
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Inventory updates should complete within 3 seconds for 100 items');
        $this->assertEquals(100, Inventory::where('quantity', '>', 100)->count());
    }

    /** @test */
    public function inventory_bulk_update_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 100 inventory items
        $inventoryIds = [];
        for ($i = 0; $i < 100; $i++) {
            $item = Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
            $inventoryIds[] = $item->id;
        }

        $startTime = microtime(true);

        // Bulk update inventory
        Inventory::whereIn('id', $inventoryIds)
            ->update([
                'is_active' => false,
                'notes' => 'Bulk updated item',
            ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk inventory updates should complete within 1 second for 100 items');
        $this->assertEquals(100, Inventory::where('is_active', false)->count());
    }

    /** @test */
    public function inventory_deletion_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 100 inventory items
        $inventory = [];
        for ($i = 0; $i < 100; $i++) {
            $inventory[] = Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Soft delete all inventory items
        foreach ($inventory as $item) {
            $item->delete();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Inventory deletions should complete within 2 seconds for 100 items');
        $this->assertEquals(0, Inventory::count());
        $this->assertEquals(100, Inventory::withTrashed()->count());
    }

    /** @test */
    public function inventory_bulk_deletion_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 100 inventory items
        $inventoryIds = [];
        for ($i = 0; $i < 100; $i++) {
            $item = Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
            $inventoryIds[] = $item->id;
        }

        $startTime = microtime(true);

        // Bulk soft delete inventory
        Inventory::whereIn('id', $inventoryIds)->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Bulk inventory deletions should complete within 1 second for 100 items');
        $this->assertEquals(0, Inventory::count());
        $this->assertEquals(100, Inventory::withTrashed()->count());
    }

    /** @test */
    public function inventory_low_stock_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items with different quantities
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => $i % 2 === 0 ? 5 : 100, // Half low stock, half normal
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Find low stock items
        $lowStockItems = Inventory::whereColumn('quantity', '<', 'min_quantity')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Low stock inventory query should complete within 1 second for 1000 items');
        $this->assertEquals(500, $lowStockItems->count());
    }

    /** @test */
    public function inventory_expiring_soon_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items with different expiry dates
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => $i % 2 === 0 ? now()->addDays(30)->format('Y-m-d') : now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Find expiring soon items (within 60 days)
        $expiringSoonItems = Inventory::where('expiry_date', '<=', now()->addDays(60))->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Expiring soon inventory query should complete within 1 second for 1000 items');
        $this->assertEquals(500, $expiringSoonItems->count());
    }

    /** @test */
    public function inventory_statistics_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => $i % 2 === 0,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Calculate statistics
        $statistics = [
            'total_items' => Inventory::count(),
            'active_items' => Inventory::where('is_active', true)->count(),
            'inactive_items' => Inventory::where('is_active', false)->count(),
            'low_stock_items' => Inventory::whereColumn('quantity', '<', 'min_quantity')->count(),
            'total_value' => Inventory::sum(DB::raw('quantity * unit_price')),
            'average_price' => Inventory::avg('unit_price'),
            'total_quantity' => Inventory::sum('quantity'),
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Inventory statistics calculation should complete within 1 second for 1000 items');
        $this->assertEquals(1000, $statistics['total_items']);
        $this->assertEquals(500, $statistics['active_items']);
        $this->assertEquals(500, $statistics['inactive_items']);
    }

    /** @test */
    public function inventory_database_query_performance_test()
    {
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        // Create 1000 inventory items
        for ($i = 0; $i < 1000; $i++) {
            Inventory::create([
                'name' => 'Item ' . $i,
                'description' => 'Professional item ' . $i,
                'category_id' => $category->id,
                'location_id' => $location->id,
                'sku' => 'SKU-' . $i,
                'barcode' => '123456789' . $i,
                'quantity' => 100 + $i,
                'min_quantity' => 10,
                'max_quantity' => 500,
                'unit_price' => 15.00 + $i,
                'supplier' => 'Supplier ' . $i,
                'supplier_contact' => '+123456789' . $i,
                'reorder_point' => 20,
                'is_active' => true,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
                'notes' => 'Performance test item ' . $i,
            ]);
        }

        $startTime = microtime(true);

        // Enable query logging
        DB::enableQueryLog();

        // Execute complex query
        $inventory = Inventory::where('is_active', true)
            ->where('quantity', '>=', 200)
            ->where('unit_price', '>=', 50)
            ->orderBy('unit_price', 'desc')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(1.0, $executionTime, 'Complex inventory query should complete within 1 second for 1000 items');
        $this->assertLessThan(5, $queryCount, 'Complex inventory query should use less than 5 database queries');
        $this->assertGreaterThan(0, $inventory->count());
    }
}
