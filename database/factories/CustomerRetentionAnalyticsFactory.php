<?php

namespace Database\Factories;

use App\Models\CustomerRetentionAnalytics;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerRetentionAnalyticsFactory extends Factory
{
    protected $model = CustomerRetentionAnalytics::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'period_start' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'period_end' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'visit_count' => $this->faker->numberBetween(0, 50),
            'total_spent' => $this->faker->randomFloat(2, 0, 10000),
            'average_spend_per_visit' => $this->faker->randomFloat(2, 50, 300),
            'days_since_last_visit' => $this->faker->numberBetween(0, 365),
            'visit_frequency_days' => $this->faker->randomFloat(1, 14, 90),
            'retention_score' => $this->faker->randomFloat(2, 0, 100),
            'churn_risk' => $this->faker->randomElement(['low', 'medium', 'high']),
            'segment' => $this->faker->randomElement(['new', 'regular', 'vip', 'at_risk', 'lost']),
            'notes' => $this->faker->optional()->paragraph(),
            'metadata' => [],
        ];
    }
}
