<?php

namespace Database\Factories;

use App\Models\LoyaltyPoint;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'invoice_id' => Invoice::factory(),
            'transaction_type' => $this->faker->randomElement(['earned', 'redeemed', 'expired', 'adjusted']),
            'points' => $this->faker->numberBetween(-500, 500),
            'source' => $this->faker->randomElement(['appointment', 'purchase', 'referral', 'bonus', 'manual_adjustment']),
            'description' => $this->faker->optional()->sentence(),
            'transaction_value' => $this->faker->optional()->randomFloat(2, 10, 500),
            'points_per_dollar' => $this->faker->randomFloat(2, 0.5, 2.0),
            'expiry_date' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'is_expired' => $this->faker->boolean(20),
            'metadata' => [],
        ];
    }
}
