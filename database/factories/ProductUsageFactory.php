<?php

namespace Database\Factories;

use App\Models\ProductUsage;
use App\Models\Product;
use App\Models\Appointment;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductUsageFactory extends Factory
{
    protected $model = ProductUsage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'appointment_id' => Appointment::factory(),
            'staff_id' => Staff::factory(),
            'quantity_used' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
