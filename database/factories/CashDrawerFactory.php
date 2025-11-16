<?php

namespace Database\Factories;

use App\Models\CashDrawer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashDrawerFactory extends Factory
{
    protected $model = CashDrawer::class;

    public function definition(): array
    {
        $openingAmount = $this->faker->randomFloat(2, 100, 500);
        $expectedAmount = $this->faker->randomFloat(2, 500, 2000);
        $closingAmount = $this->faker->randomFloat(2, 500, 2000);
        $difference = $closingAmount - $expectedAmount;

        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'opened_at' => '09:00:00',
            'closed_at' => '18:00:00',
            'opening_amount' => $openingAmount,
            'closing_amount' => $closingAmount,
            'expected_amount' => $expectedAmount,
            'difference' => $difference,
            'status' => $this->faker->randomElement(['open', 'closed']),
            'opening_notes' => $this->faker->optional()->sentence(),
            'closing_notes' => $this->faker->optional()->sentence(),
        ];
    }
}
