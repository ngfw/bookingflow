<?php

namespace Database\Factories;

use App\Models\StaffPerformance;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffPerformanceFactory extends Factory
{
    protected $model = StaffPerformance::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'period_start' => $this->faker->dateTimeBetween('-1 month', '-15 days'),
            'period_end' => $this->faker->dateTimeBetween('-14 days', 'now'),
            'total_appointments' => $this->faker->numberBetween(10, 200),
            'completed_appointments' => $this->faker->numberBetween(5, 150),
            'cancelled_appointments' => $this->faker->numberBetween(0, 20),
            'total_revenue' => $this->faker->randomFloat(2, 1000, 20000),
            'average_rating' => $this->faker->randomFloat(2, 3.0, 5.0),
            'total_reviews' => $this->faker->numberBetween(0, 50),
            'performance_score' => $this->faker->randomFloat(2, 60, 100),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
