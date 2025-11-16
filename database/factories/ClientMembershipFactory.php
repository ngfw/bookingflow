<?php

namespace Database\Factories;

use App\Models\ClientMembership;
use App\Models\Client;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientMembershipFactory extends Factory
{
    protected $model = ClientMembership::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'membership_tier_id' => MembershipTier::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('now', '+2 years'),
            'status' => $this->faker->randomElement(['active', 'expired', 'suspended', 'cancelled']),
            'total_spent' => $this->faker->randomFloat(2, 0, 10000),
            'total_visits' => $this->faker->numberBetween(0, 100),
            'total_points_earned' => $this->faker->numberBetween(0, 5000),
            'last_visit_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'next_review_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'metadata' => [],
        ];
    }
}
