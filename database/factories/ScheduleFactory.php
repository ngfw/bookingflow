<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'break_start' => $this->faker->optional()->randomElement(['12:00', '13:00']),
            'break_end' => $this->faker->optional()->randomElement(['12:30', '13:30']),
            'status' => $this->faker->randomElement(['available', 'scheduled', 'unavailable', 'sick', 'vacation']),
            'notes' => $this->faker->optional()->sentence(),
            'is_recurring' => $this->faker->boolean(20),
            'recurring_type' => $this->faker->optional()->randomElement(['daily', 'weekly', 'monthly']),
            'recurring_end_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
