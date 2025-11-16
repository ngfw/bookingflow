<?php

namespace Database\Factories;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityEventFactory extends Factory
{
    protected $model = SecurityEvent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => $this->faker->randomElement(['login_attempt', 'login_success', 'login_failed', 'logout', 'password_change', 'suspicious_activity']),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'details' => [],
            'resolved' => $this->faker->boolean(70),
            'resolved_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
