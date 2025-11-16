<?php

namespace Database\Factories;

use App\Models\AnalyticsSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalyticsSessionFactory extends Factory
{
    protected $model = AnalyticsSession::class;

    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $endedAt = $this->faker->optional()->dateTimeBetween($startedAt, 'now');
        $duration = $endedAt ? $startedAt->diff($endedAt)->s : null;

        return [
            'session_id' => $this->faker->uuid(),
            'user_id' => $this->faker->optional()->randomNumber(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'device_info' => [
                'device_type' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            ],
            'location_info' => [
                'country' => $this->faker->country(),
                'city' => $this->faker->city(),
            ],
            'referrer' => $this->faker->optional()->url(),
            'landing_page' => $this->faker->url(),
            'page_views' => $this->faker->numberBetween(1, 20),
            'duration' => $duration,
            'is_bounce' => $this->faker->boolean(30),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }
}
