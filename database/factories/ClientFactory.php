<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'preferences' => [
                'preferred_stylist' => $this->faker->name(),
                'preferred_time' => $this->faker->time('H:i'),
                'communication_method' => $this->faker->randomElement(['email', 'sms', 'phone']),
            ],
            'allergies' => $this->faker->optional()->sentence(),
            'medical_conditions' => $this->faker->optional()->sentence(),
            'last_visit' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'total_spent' => $this->faker->randomFloat(2, 0, 5000),
            'visit_count' => $this->faker->numberBetween(0, 50),
            'loyalty_points' => $this->faker->numberBetween(0, 1000),
            'preferred_contact' => $this->faker->randomElement(['email', 'sms', 'phone', 'whatsapp']),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
