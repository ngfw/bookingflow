<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-###??###')),
            'barcode' => $this->faker->optional()->ean13(),
            'brand' => $this->faker->optional()->company(),
            'cost_price' => $this->faker->randomFloat(2, 5, 100),
            'selling_price' => $this->faker->randomFloat(2, 10, 200),
            'retail_price' => $this->faker->randomFloat(2, 15, 300),
            'current_stock' => $this->faker->numberBetween(0, 100),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'minimum_stock' => $this->faker->numberBetween(5, 20),
            'unit' => $this->faker->randomElement(['piece', 'bottle', 'box', 'tube', 'jar']),
            'supplier' => $this->faker->optional()->company(),
            'expiry_date' => $this->faker->optional()->dateTimeBetween('+3 months', '+2 years'),
            'storage_location' => $this->faker->optional()->randomElement(['Shelf A', 'Shelf B', 'Cabinet 1', 'Storage Room']),
            'is_for_sale' => $this->faker->boolean(70),
            'is_for_service' => $this->faker->boolean(80),
            'type' => $this->faker->randomElement(['product', 'service', 'both']),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'products'),
            'is_active' => $this->faker->boolean(90),
            'usage_notes' => $this->faker->optional()->sentence(),
        ];
    }
}
