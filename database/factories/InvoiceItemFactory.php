<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->randomFloat(2, 10, 200);
        $totalPrice = $quantity * $unitPrice;

        return [
            'invoice_id' => Invoice::factory(),
            'item_type' => $this->faker->randomElement(['service', 'product']),
            'item_name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'service_id' => $this->faker->optional()->randomNumber(),
            'product_id' => $this->faker->optional()->randomNumber(),
        ];
    }
}
