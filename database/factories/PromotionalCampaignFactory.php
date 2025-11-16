<?php

namespace Database\Factories;

use App\Models\PromotionalCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionalCampaignFactory extends Factory
{
    protected $model = PromotionalCampaign::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['discount', 'bonus_points', 'free_service', 'gift']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'scheduled', 'expired']),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'target_criteria' => [],
            'discount_settings' => ['percentage' => $this->faker->numberBetween(10, 30)],
            'bonus_points_settings' => [],
            'conditions' => [],
            'usage_limit' => $this->faker->optional()->numberBetween(1, 1000),
            'usage_count' => $this->faker->numberBetween(0, 500),
            'metadata' => [],
        ];
    }
}
