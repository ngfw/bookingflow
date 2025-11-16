<?php

namespace Database\Factories;

use App\Models\ReminderSchedule;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderScheduleFactory extends Factory
{
    protected $model = ReminderSchedule::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'reminder_type' => $this->faker->randomElement(['email', 'sms', 'push']),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'sent_at' => $this->faker->optional()->dateTime(),
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed', 'cancelled']),
            'message' => $this->faker->paragraph(),
        ];
    }
}
