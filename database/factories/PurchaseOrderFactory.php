<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return [
            'po_number' => 'PO' . $this->faker->unique()->numerify('######'),
            'supplier_id' => Supplier::factory(),
            'created_by' => User::factory(),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'expected_delivery_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'actual_delivery_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['draft', 'pending', 'ordered', 'received', 'cancelled']),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'shipping_cost' => $this->faker->randomFloat(2, 0, 100),
            'total_amount' => $total,
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
