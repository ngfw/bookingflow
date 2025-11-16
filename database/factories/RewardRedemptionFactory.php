<?php

namespace Database\Factories;

use App\Models\RewardRedemption;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class RewardRedemptionFactory extends Factory
{
    protected $model = RewardRedemption::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'points_redeemed' => $this->faker->numberBetween(50, 500),
            'reward_type' => $this->faker->randomElement(['discount', 'free_service', 'product', 'voucher']),
            'reward_value' => $this->faker->randomFloat(2, 10, 200),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'completed', 'cancelled']),
            'redeemed_at' => $this->faker->dateTime(),
        ];
    }
}
