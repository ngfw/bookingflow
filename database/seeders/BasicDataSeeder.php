<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Hair Services', 'description' => 'Hair cutting, styling, and coloring services', 'color' => '#FF6B6B', 'is_active' => true],
            ['name' => 'Skin Care', 'description' => 'Facial treatments and skin care services', 'color' => '#4ECDC4', 'is_active' => true],
            ['name' => 'Nail Services', 'description' => 'Manicures, pedicures, and nail art', 'color' => '#45B7D1', 'is_active' => true],
            ['name' => 'Massage', 'description' => 'Relaxation and therapeutic massage services', 'color' => '#96CEB4', 'is_active' => true],
            ['name' => 'Makeup', 'description' => 'Makeup application and beauty services', 'color' => '#FFEAA7', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create services
        $services = [
            // Hair Services
            ['category_id' => 1, 'name' => 'Haircut', 'description' => 'Professional haircut and styling', 'price' => 45.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 1, 'name' => 'Hair Color', 'description' => 'Full hair coloring service', 'price' => 120.00, 'duration_minutes' => 120, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 1, 'name' => 'Hair Highlights', 'description' => 'Partial or full highlights', 'price' => 80.00, 'duration_minutes' => 90, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 1, 'name' => 'Blowout', 'description' => 'Professional blow dry and styling', 'price' => 35.00, 'duration_minutes' => 45, 'online_booking_enabled' => true, 'is_active' => true],
            
            // Skin Care
            ['category_id' => 2, 'name' => 'Facial Treatment', 'description' => 'Deep cleansing facial treatment', 'price' => 85.00, 'duration_minutes' => 75, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Anti-Aging Facial', 'description' => 'Specialized anti-aging facial treatment', 'price' => 120.00, 'duration_minutes' => 90, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Acne Treatment', 'description' => 'Targeted acne treatment facial', 'price' => 95.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            
            // Nail Services
            ['category_id' => 3, 'name' => 'Manicure', 'description' => 'Classic manicure with polish', 'price' => 30.00, 'duration_minutes' => 45, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Pedicure', 'description' => 'Classic pedicure with polish', 'price' => 45.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Gel Manicure', 'description' => 'Long-lasting gel manicure', 'price' => 50.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            
            // Massage
            ['category_id' => 4, 'name' => 'Swedish Massage', 'description' => 'Relaxing Swedish massage', 'price' => 80.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 4, 'name' => 'Deep Tissue Massage', 'description' => 'Therapeutic deep tissue massage', 'price' => 100.00, 'duration_minutes' => 75, 'online_booking_enabled' => true, 'is_active' => true],
            
            // Makeup
            ['category_id' => 5, 'name' => 'Makeup Application', 'description' => 'Professional makeup application', 'price' => 60.00, 'duration_minutes' => 60, 'online_booking_enabled' => true, 'is_active' => true],
            ['category_id' => 5, 'name' => 'Bridal Makeup', 'description' => 'Special bridal makeup package', 'price' => 150.00, 'duration_minutes' => 120, 'online_booking_enabled' => true, 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create suppliers
        $suppliers = [
            ['name' => 'Beauty Supply Co', 'contact_person' => 'John Smith', 'email' => 'john@beautysupply.com', 'phone' => '+1-555-0101', 'address' => '123 Supply St', 'city' => 'Beauty City', 'state' => 'BC', 'postal_code' => '12345', 'country' => 'USA', 'is_active' => true],
            ['name' => 'Professional Products Inc', 'contact_person' => 'Sarah Johnson', 'email' => 'sarah@proproducts.com', 'phone' => '+1-555-0102', 'address' => '456 Professional Ave', 'city' => 'Pro City', 'state' => 'PC', 'postal_code' => '67890', 'country' => 'USA', 'is_active' => true],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create products
        $products = [
            ['category_id' => 1, 'supplier_id' => 1, 'name' => 'Shampoo', 'description' => 'Professional salon shampoo', 'sku' => 'SH001', 'cost_price' => 8.50, 'selling_price' => 15.00, 'retail_price' => 18.00, 'current_stock' => 50, 'stock_quantity' => 50, 'minimum_stock' => 10, 'unit' => 'bottle', 'is_for_sale' => true, 'is_for_service' => true, 'is_active' => true],
            ['category_id' => 1, 'supplier_id' => 1, 'name' => 'Conditioner', 'description' => 'Professional salon conditioner', 'sku' => 'CO001', 'cost_price' => 9.00, 'selling_price' => 16.00, 'retail_price' => 19.00, 'current_stock' => 45, 'stock_quantity' => 45, 'minimum_stock' => 10, 'unit' => 'bottle', 'is_for_sale' => true, 'is_for_service' => true, 'is_active' => true],
            ['category_id' => 2, 'supplier_id' => 2, 'name' => 'Facial Cleanser', 'description' => 'Gentle facial cleanser', 'sku' => 'FC001', 'cost_price' => 12.00, 'selling_price' => 22.00, 'retail_price' => 25.00, 'current_stock' => 30, 'stock_quantity' => 30, 'minimum_stock' => 5, 'unit' => 'tube', 'is_for_sale' => true, 'is_for_service' => true, 'is_active' => true],
            ['category_id' => 3, 'supplier_id' => 1, 'name' => 'Nail Polish', 'description' => 'Professional nail polish', 'sku' => 'NP001', 'cost_price' => 3.50, 'selling_price' => 8.00, 'retail_price' => 10.00, 'current_stock' => 100, 'stock_quantity' => 100, 'minimum_stock' => 20, 'unit' => 'bottle', 'is_for_sale' => true, 'is_for_service' => true, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create staff records for existing users
        $staffUser = User::where('email', 'staff@beautysalon.com')->first();
        if ($staffUser) {
            Staff::create([
                'user_id' => $staffUser->id,
                'employee_id' => 'EMP001',
                'position' => 'Senior Stylist',
                'specializations' => json_encode(['Hair Cutting', 'Hair Coloring', 'Styling']),
                'hourly_rate' => 25.00,
                'commission_rate' => 0.15,
                'hire_date' => '2023-01-15',
                'employment_type' => 'full-time',
                'default_start_time' => '09:00',
                'default_end_time' => '17:00',
                'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'can_book_online' => true,
                'bio' => 'Experienced stylist with 5+ years in the beauty industry.',
            ]);
        }

        // Create client record for existing client user
        $clientUser = User::where('email', 'client@beautysalon.com')->first();
        if ($clientUser) {
            Client::create([
                'user_id' => $clientUser->id,
                'preferences' => json_encode(['Hair Services', 'Skin Care']),
                'allergies' => json_encode([]),
                'medical_conditions' => json_encode([]),
                'last_visit' => null,
                'total_spent' => 0.00,
                'visit_count' => 0,
                'loyalty_points' => 0,
                'preferred_contact' => 'email',
                'notes' => 'New client - welcome!',
            ]);
        }
    }
}