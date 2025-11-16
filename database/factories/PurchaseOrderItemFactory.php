<?php

namespace Database\Factories;

use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->randomFloat(2, 10, 200);
        $totalPrice = $quantity * $unitPrice;

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'quantity_ordered' => $quantity,
            'quantity_received' => $this->faker->numberBetween(0, $quantity),
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
