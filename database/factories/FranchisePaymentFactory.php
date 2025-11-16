<?php

namespace Database\Factories;

use App\Models\FranchisePayment;
use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchisePaymentFactory extends Factory
{
    protected $model = FranchisePayment::class;

    public function definition(): array
    {
        return [
            'franchise_id' => Franchise::factory(),
            'payment_type' => $this->faker->randomElement(['franchise_fee', 'royalty', 'marketing_fee', 'other']),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'due_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'paid_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['pending', 'paid', 'overdue', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'check', 'card', 'cash']),
            'reference_number' => $this->faker->optional()->numerify('FP######'),
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => [],
        ];
    }
}
