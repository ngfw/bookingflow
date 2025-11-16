<?php

namespace Database\Factories;

use App\Models\AccessLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessLogFactory extends Factory
{
    protected $model = AccessLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_type' => $this->faker->randomElement(['permission_granted', 'permission_revoked', 'access_granted', 'access_denied', 'resource_access']),
            'permission' => $this->faker->randomElement(['view_appointments', 'edit_clients', 'manage_staff', 'view_reports', 'manage_settings']),
            'resource' => $this->faker->randomElement(['appointments', 'clients', 'staff', 'invoices', 'settings']),
            'result' => $this->faker->randomElement(['granted', 'denied', 'attempted']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'url' => $this->faker->url(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
        ];
    }
}
