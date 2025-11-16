<?php

namespace Database\Factories;

use App\Models\SalonSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalonSettingFactory extends Factory
{
    protected $model = SalonSetting::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->sentence(),
            'group' => $this->faker->randomElement(['general', 'booking', 'notifications', 'payments', 'appearance']),
            'type' => $this->faker->randomElement(['string', 'number', 'boolean', 'json']),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
