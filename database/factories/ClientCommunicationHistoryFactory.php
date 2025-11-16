<?php

namespace Database\Factories;

use App\Models\ClientCommunicationHistory;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientCommunicationHistoryFactory extends Factory
{
    protected $model = ClientCommunicationHistory::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'staff_id' => Staff::factory(),
            'appointment_id' => Appointment::factory(),
            'communication_type' => $this->faker->randomElement(['email', 'sms', 'phone', 'in_person', 'push_notification', 'system_generated']),
            'direction' => $this->faker->randomElement(['inbound', 'outbound']),
            'subject' => $this->faker->optional()->sentence(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['sent', 'delivered', 'read', 'failed', 'pending']),
            'channel' => $this->faker->randomElement(['email', 'sms', 'phone', 'in_person', 'push']),
            'recipient' => $this->faker->email(),
            'sender' => $this->faker->name(),
            'metadata' => [],
            'sent_at' => $this->faker->optional()->dateTime(),
            'delivered_at' => $this->faker->optional()->dateTime(),
            'read_at' => $this->faker->optional()->dateTime(),
            'notes' => $this->faker->optional()->sentence(),
            'is_important' => $this->faker->boolean(20),
            'requires_follow_up' => $this->faker->boolean(30),
            'follow_up_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'follow_up_notes' => $this->faker->optional()->sentence(),
        ];
    }
}
