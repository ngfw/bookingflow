<?php

namespace Database\Factories;

use App\Models\NotificationLog;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationLogFactory extends Factory
{
    protected $model = NotificationLog::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'type' => $this->faker->randomElement(['email', 'sms', 'push']),
            'subject' => $this->faker->optional()->sentence(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed', 'delivered']),
            'sent_at' => $this->faker->optional()->dateTime(),
            'error_message' => $this->faker->optional()->sentence(),
        ];
    }
}
