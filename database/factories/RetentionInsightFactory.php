<?php

namespace Database\Factories;

use App\Models\RetentionInsight;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class RetentionInsightFactory extends Factory
{
    protected $model = RetentionInsight::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'insight_type' => $this->faker->randomElement(['churn_risk', 'engagement', 'loyalty', 'spending_pattern']),
            'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'data' => [],
            'recommendations' => [$this->faker->sentence(), $this->faker->sentence()],
            'generated_at' => $this->faker->dateTime(),
        ];
    }
}
