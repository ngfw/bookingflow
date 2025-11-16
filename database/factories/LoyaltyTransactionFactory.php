<?php

namespace Database\Factories;

use App\Models\LoyaltyTransaction;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyTransactionFactory extends Factory
{
    protected $model = LoyaltyTransaction::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'points' => $this->faker->numberBetween(-100, 500),
            'type' => $this->faker->randomElement(['earned', 'redeemed', 'expired']),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
