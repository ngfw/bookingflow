<?php

namespace Database\Factories;

use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalyticsEventFactory extends Factory
{
    protected $model = AnalyticsEvent::class;

    public function definition(): array
    {
        return [
            'event_type' => $this->faker->randomElement(['page_view', 'click', 'form_submit', 'booking', 'purchase']),
            'event_name' => $this->faker->words(3, true),
            'event_data' => [
                'action' => $this->faker->word(),
                'value' => $this->faker->numberBetween(1, 100),
            ],
            'page_url' => $this->faker->url(),
            'page_title' => $this->faker->sentence(),
            'user_agent' => $this->faker->userAgent(),
            'ip_address' => $this->faker->ipv4(),
            'referrer' => $this->faker->optional()->url(),
            'session_id' => $this->faker->uuid(),
            'user_id' => $this->faker->optional()->randomNumber(),
            'device_info' => [
                'device_type' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            ],
            'location_info' => [
                'country' => $this->faker->country(),
                'city' => $this->faker->city(),
            ],
        ];
    }
}
