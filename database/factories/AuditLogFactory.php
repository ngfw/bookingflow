<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_name' => $this->faker->name(),
            'user_email' => $this->faker->email(),
            'action' => $this->faker->randomElement(['create', 'update', 'delete', 'login', 'logout']),
            'model_type' => $this->faker->randomElement(['App\Models\Client', 'App\Models\Appointment', 'App\Models\Service']),
            'model_id' => $this->faker->numberBetween(1, 100),
            'old_data' => ['status' => 'pending'],
            'new_data' => ['status' => 'completed'],
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'url' => $this->faker->url(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'metadata' => [],
        ];
    }
}
