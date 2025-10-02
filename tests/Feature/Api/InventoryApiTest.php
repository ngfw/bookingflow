<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_inventory_item_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $inventoryData = [
            'name' => 'Shampoo',
            'description' => 'Professional hair shampoo',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'sku' => 'SHM-001',
            'barcode' => '1234567890123',
            'quantity' => 100,
            'min_quantity' => 10,
            'max_quantity' => 500,
            'unit_price' => 15.00,
            'supplier' => 'Beauty Supply Co',
            'supplier_contact' => '+1234567890',
            'reorder_point' => 20,
            'is_active' => true,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
            'notes' => 'High-quality professional shampoo',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/inventory', $inventoryData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'inventory' => [
                        'id',
                        'name',
                        'description',
                        'category_id',
                        'location_id',
                        'sku',
                        'barcode',
                        'quantity',
                        'min_quantity',
                        'max_quantity',
                        'unit_price',
                        'supplier',
                        'supplier_contact',
                        'reorder_point',
                        'is_active',
                        'expiry_date',
                        'notes',
                        'created_at',
                    ],
                ]);

        $this->assertDatabaseHas('inventory', [
            'name' => 'Shampoo',
            'description' => 'Professional hair shampoo',
            'sku' => 'SHM-001',
            'barcode' => '1234567890123',
            'quantity' => 100,
        ]);
    }

    /** @test */
    public function staff_can_create_inventory_item_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $inventoryData = [
            'name' => 'Conditioner',
            'description' => 'Professional hair conditioner',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'sku' => 'CON-001',
            'barcode' => '1234567890124',
            'quantity' => 50,
            'min_quantity' => 5,
            'max_quantity' => 200,
            'unit_price' => 18.00,
            'supplier' => 'Beauty Supply Co',
            'supplier_contact' => '+1234567890',
            'reorder_point' => 10,
            'is_active' => true,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
            'notes' => 'Professional conditioner',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/inventory', $inventoryData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'inventory' => [
                        'id',
                        'name',
                        'description',
                        'category_id',
                        'location_id',
                        'sku',
                        'barcode',
                        'quantity',
                        'min_quantity',
                        'max_quantity',
                        'unit_price',
                        'supplier',
                        'supplier_contact',
                        'reorder_point',
                        'is_active',
                        'expiry_date',
                        'notes',
                        'created_at',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'inventory' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'category_id',
                            'location_id',
                            'sku',
                            'barcode',
                            'quantity',
                            'min_quantity',
                            'max_quantity',
                            'unit_price',
                            'supplier',
                            'supplier_contact',
                            'reorder_point',
                            'is_active',
                            'expiry_date',
                            'notes',
                            'created_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);
    }

    /** @test */
    public function staff_can_view_inventory_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $inventory = Inventory::factory()->count(3)->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'inventory' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'category_id',
                            'location_id',
                            'sku',
                            'barcode',
                            'quantity',
                            'min_quantity',
                            'max_quantity',
                            'unit_price',
                            'supplier',
                            'supplier_contact',
                            'reorder_point',
                            'is_active',
                            'expiry_date',
                            'notes',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_inventory_details_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory/' . $inventory->id);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'inventory' => [
                        'id',
                        'name',
                        'description',
                        'category_id',
                        'location_id',
                        'sku',
                        'barcode',
                        'quantity',
                        'min_quantity',
                        'max_quantity',
                        'unit_price',
                        'supplier',
                        'supplier_contact',
                        'reorder_point',
                        'is_active',
                        'expiry_date',
                        'notes',
                        'created_at',
                        'category' => [
                            'id',
                            'name',
                            'description',
                        ],
                        'location' => [
                            'id',
                            'name',
                            'address',
                        ],
                        'movements' => [
                            '*' => [
                                'id',
                                'type',
                                'quantity',
                                'reason',
                                'created_at',
                            ],
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_update_inventory_item_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Item Name',
            'quantity' => 150,
            'unit_price' => 20.00,
            'supplier' => 'New Supplier',
            'notes' => 'Updated notes',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/inventory/' . $inventory->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'name' => 'Updated Item Name',
                        'quantity' => 150,
                        'unit_price' => 20.00,
                        'supplier' => 'New Supplier',
                        'notes' => 'Updated notes',
                    ],
                ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory->id,
            'name' => 'Updated Item Name',
            'quantity' => 150,
            'unit_price' => 20.00,
            'supplier' => 'New Supplier',
            'notes' => 'Updated notes',
        ]);
    }

    /** @test */
    public function staff_can_update_inventory_item_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $inventory = Inventory::factory()->create();
        $token = $staff->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Staff Updated Item',
            'quantity' => 75,
            'unit_price' => 25.00,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/inventory/' . $inventory->id, $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'name' => 'Staff Updated Item',
                        'quantity' => 75,
                        'unit_price' => 25.00,
                    ],
                ]);
    }

    /** @test */
    public function admin_can_delete_inventory_item_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/inventory/' . $inventory->id);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Inventory item deleted successfully',
                ]);

        $this->assertSoftDeleted('inventory', ['id' => $inventory->id]);
    }

    /** @test */
    public function admin_can_search_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Inventory::factory()->create(['name' => 'Shampoo']);
        Inventory::factory()->create(['name' => 'Conditioner']);
        Inventory::factory()->create(['name' => 'Hair Oil']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory?search=Shampoo');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'inventory')
                ->assertJsonPath('inventory.0.name', 'Shampoo');
    }

    /** @test */
    public function admin_can_filter_inventory_by_category_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category1 = Category::factory()->create(['name' => 'Hair Care']);
        $category2 = Category::factory()->create(['name' => 'Skin Care']);
        Inventory::factory()->create(['category_id' => $category1->id]);
        Inventory::factory()->create(['category_id' => $category2->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory?category_id=' . $category1->id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'inventory')
                ->assertJsonPath('inventory.0.category_id', $category1->id);
    }

    /** @test */
    public function admin_can_filter_inventory_by_location_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $location1 = Location::factory()->create(['name' => 'Downtown']);
        $location2 = Location::factory()->create(['name' => 'Uptown']);
        Inventory::factory()->create(['location_id' => $location1->id]);
        Inventory::factory()->create(['location_id' => $location2->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory?location_id=' . $location1->id);

        $response->assertStatus(200)
                ->assertJsonCount(1, 'inventory')
                ->assertJsonPath('inventory.0.location_id', $location1->id);
    }

    /** @test */
    public function admin_can_filter_inventory_by_low_stock_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Inventory::factory()->create(['quantity' => 5, 'min_quantity' => 10]);
        Inventory::factory()->create(['quantity' => 50, 'min_quantity' => 10]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory?low_stock=true');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'inventory')
                ->assertJsonPath('inventory.0.quantity', 5);
    }

    /** @test */
    public function admin_can_filter_inventory_by_expiring_soon_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Inventory::factory()->create(['expiry_date' => now()->addDays(30)]);
        Inventory::factory()->create(['expiry_date' => now()->addYear()]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory?expiring_soon=true');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'inventory');
    }

    /** @test */
    public function admin_can_export_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Inventory::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory/export');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_bulk_update_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory1 = Inventory::factory()->create();
        $inventory2 = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/inventory/bulk-update', [
            'inventory_ids' => [$inventory1->id, $inventory2->id],
            'is_active' => false,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Inventory items updated successfully',
                ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('inventory', [
            'id' => $inventory2->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_bulk_delete_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory1 = Inventory::factory()->create();
        $inventory2 = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/inventory/bulk-delete', [
            'inventory_ids' => [$inventory1->id, $inventory2->id],
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Inventory items deleted successfully',
                ]);

        $this->assertSoftDeleted('inventory', ['id' => $inventory1->id]);
        $this->assertSoftDeleted('inventory', ['id' => $inventory2->id]);
    }

    /** @test */
    public function admin_can_view_inventory_statistics_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Inventory::factory()->count(3)->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'statistics' => [
                        'total_items',
                        'active_items',
                        'inactive_items',
                        'low_stock_items',
                        'expiring_soon_items',
                        'total_value',
                        'items_by_category',
                        'items_by_location',
                    ],
                ]);
    }

    /** @test */
    public function admin_can_adjust_inventory_quantity_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['quantity' => 100]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/inventory/' . $inventory->id . '/adjust', [
            'quantity' => 50,
            'reason' => 'Stock adjustment',
            'type' => 'out',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'quantity' => 50,
                    ],
                ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory->id,
            'quantity' => 50,
        ]);
    }

    /** @test */
    public function staff_can_adjust_inventory_quantity_via_api()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $inventory = Inventory::factory()->create(['quantity' => 100]);
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/inventory/' . $inventory->id . '/adjust', [
            'quantity' => 25,
            'reason' => 'Used in service',
            'type' => 'out',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'quantity' => 25,
                    ],
                ]);
    }

    /** @test */
    public function admin_can_view_inventory_movements_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory/' . $inventory->id . '/movements');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'movements' => [
                        '*' => [
                            'id',
                            'type',
                            'quantity',
                            'reason',
                            'created_at',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function admin_can_reorder_inventory_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['quantity' => 5, 'reorder_point' => 10]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/inventory/' . $inventory->id . '/reorder', [
            'quantity' => 100,
            'expected_delivery' => now()->addWeek()->format('Y-m-d'),
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Reorder request created successfully',
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_inventory_api()
    {
        $response = $this->getJson('/api/inventory');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated',
                ]);
    }

    /** @test */
    public function client_cannot_access_inventory_api()
    {
        $client = User::factory()->create(['role' => 'client']);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/inventory');

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Forbidden',
                ]);
    }

    /** @test */
    public function admin_can_archive_inventory_item_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['is_active' => true]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/inventory/' . $inventory->id . '/archive');

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'is_active' => false,
                    ],
                ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_restore_inventory_item_via_api()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['is_active' => false]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/inventory/' . $inventory->id . '/restore');

        $response->assertStatus(200)
                ->assertJson([
                    'inventory' => [
                        'id' => $inventory->id,
                        'is_active' => true,
                    ],
                ]);

        $this->assertDatabaseHas('inventory', [
            'id' => $inventory->id,
            'is_active' => true,
        ]);
    }
}
