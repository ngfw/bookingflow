<?php

namespace Database\Factories;

use App\Models\StaffPayroll;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffPayrollFactory extends Factory
{
    protected $model = StaffPayroll::class;

    public function definition(): array
    {
        $baseAmount = $this->faker->randomFloat(2, 1000, 5000);
        $commission = $this->faker->randomFloat(2, 0, 1000);
        $bonus = $this->faker->randomFloat(2, 0, 500);
        $deductions = $this->faker->randomFloat(2, 0, 300);
        $totalAmount = $baseAmount + $commission + $bonus - $deductions;

        return [
            'staff_id' => Staff::factory(),
            'period_start' => $this->faker->dateTimeBetween('-1 month', '-15 days'),
            'period_end' => $this->faker->dateTimeBetween('-14 days', 'now'),
            'base_amount' => $baseAmount,
            'commission_amount' => $commission,
            'bonus_amount' => $bonus,
            'deductions' => $deductions,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved', 'paid']),
            'paid_at' => $this->faker->optional()->dateTime(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
