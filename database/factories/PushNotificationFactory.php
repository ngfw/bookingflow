<?php

namespace Database\Factories;

use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PushNotificationFactory extends Factory
{
    protected $model = PushNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'data' => [],
            'sent_at' => $this->faker->optional()->dateTime(),
            'read_at' => $this->faker->optional()->dateTime(),
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed', 'read']),
        ];
    }
}
