<?php

namespace Database\Factories;

use App\Models\FranchiseMetric;
use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchiseMetricFactory extends Factory
{
    protected $model = FranchiseMetric::class;

    public function definition(): array
    {
        return [
            'franchise_id' => Franchise::factory(),
            'period_start' => $this->faker->dateTimeBetween('-1 month', '-15 days'),
            'period_end' => $this->faker->dateTimeBetween('-14 days', 'now'),
            'total_revenue' => $this->faker->randomFloat(2, 5000, 50000),
            'total_appointments' => $this->faker->numberBetween(50, 500),
            'total_clients' => $this->faker->numberBetween(20, 200),
            'new_clients' => $this->faker->numberBetween(5, 50),
            'recurring_clients' => $this->faker->numberBetween(15, 150),
            'average_transaction_value' => $this->faker->randomFloat(2, 50, 200),
            'royalty_amount' => $this->faker->randomFloat(2, 500, 5000),
            'marketing_fee_amount' => $this->faker->randomFloat(2, 100, 1000),
            'metrics_data' => [],
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
