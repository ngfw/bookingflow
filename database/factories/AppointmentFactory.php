<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $appointmentDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endTime = (clone $appointmentDate)->modify('+1 hour');

        return [
            'client_id' => Client::factory(),
            'staff_id' => Staff::factory(),
            'service_id' => Service::factory(),
            'appointment_number' => 'APT' . $this->faker->unique()->numerify('######'),
            'appointment_date' => $appointmentDate,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show']),
            'booking_source' => $this->faker->randomElement(['online', 'phone', 'walk_in', 'admin']),
            'service_price' => $this->faker->randomFloat(2, 50, 500),
            'deposit_paid' => $this->faker->randomFloat(2, 0, 100),
            'client_notes' => $this->faker->optional()->sentence(),
            'staff_notes' => $this->faker->optional()->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'is_recurring' => $this->faker->boolean(20),
            'recurring_pattern' => $this->faker->optional()->randomElement(['daily', 'weekly', 'monthly']),
            'recurring_end_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months'),
            'reminder_sent' => $this->faker->boolean(),
            'reminder_sent_at' => $this->faker->optional()->dateTime(),
            'follow_up_required' => $this->faker->boolean(30),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => $this->faker->sentence(),
            'cancelled_at' => $this->faker->dateTime(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
