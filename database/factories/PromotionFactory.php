<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->unique()->lexify('PROMO???')),
            'description' => $this->faker->paragraph(),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed_amount']),
            'discount_value' => $this->faker->randomFloat(2, 5, 50),
            'min_purchase_amount' => $this->faker->optional()->randomFloat(2, 0, 100),
            'max_discount_amount' => $this->faker->optional()->randomFloat(2, 10, 100),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'usage_limit' => $this->faker->optional()->numberBetween(1, 100),
            'usage_count' => $this->faker->numberBetween(0, 50),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
