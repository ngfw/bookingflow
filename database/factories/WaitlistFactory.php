<?php

namespace Database\Factories;

use App\Models\Waitlist;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaitlistFactory extends Factory
{
    protected $model = Waitlist::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'service_id' => Service::factory(),
            'preferred_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'preferred_time' => $this->faker->randomElement(['morning', 'afternoon', 'evening', 'any']),
            'status' => $this->faker->randomElement(['waiting', 'contacted', 'booked', 'cancelled', 'expired']),
            'notes' => $this->faker->optional()->sentence(),
            'contacted_at' => $this->faker->optional()->dateTime(),
            'expires_at' => $this->faker->optional()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }
}
