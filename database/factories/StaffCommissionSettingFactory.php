<?php

namespace Database\Factories;

use App\Models\StaffCommissionSetting;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffCommissionSettingFactory extends Factory
{
    protected $model = StaffCommissionSetting::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'service_id' => Service::factory(),
            'commission_type' => $this->faker->randomElement(['percentage', 'fixed_amount']),
            'commission_value' => $this->faker->randomFloat(2, 5, 50),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
