<?php

namespace Database\Factories;

use App\Models\BirthdayAnniversarySpecial;
use Illuminate\Database\Eloquent\Factories\Factory;

class BirthdayAnniversarySpecialFactory extends Factory
{
    protected $model = BirthdayAnniversarySpecial::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) . ' Special',
            'type' => $this->faker->randomElement(['birthday', 'anniversary', 'both']),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'expired']),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
            'days_before' => $this->faker->numberBetween(0, 7),
            'days_after' => $this->faker->numberBetween(0, 7),
            'discount_settings' => [
                'percentage' => $this->faker->numberBetween(10, 30),
            ],
            'bonus_points_settings' => [
                'multiplier' => $this->faker->randomFloat(1, 1.5, 3.0),
            ],
            'free_service_settings' => [],
            'gift_settings' => [],
            'min_purchase_amount' => $this->faker->randomFloat(2, 0, 50),
            'max_discount_amount' => $this->faker->randomFloat(2, 50, 200),
            'usage_limit_per_client' => $this->faker->numberBetween(1, 5),
            'requires_appointment' => $this->faker->boolean(),
            'auto_apply' => $this->faker->boolean(),
            'notification_settings' => [],
            'target_criteria' => [],
            'metadata' => [],
        ];
    }
}
