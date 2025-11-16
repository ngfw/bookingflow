<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    public function definition(): array
    {
        return [
            'referrer_id' => Client::factory(),
            'referred_id' => Client::factory(),
            'referral_code' => strtoupper($this->faker->unique()->lexify('REF???###')),
            'status' => $this->faker->randomElement(['pending', 'completed', 'rewarded', 'expired']),
            'reward_amount' => $this->faker->randomFloat(2, 10, 100),
            'reward_points' => $this->faker->numberBetween(50, 500),
            'rewarded_at' => $this->faker->optional()->dateTime(),
            'expires_at' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
        ];
    }
}
