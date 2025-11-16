<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'duration_minutes' => $this->faker->randomElement([30, 45, 60, 90, 120, 180]),
            'buffer_time_minutes' => $this->faker->randomElement([0, 15, 30]),
            'requires_deposit' => $this->faker->boolean(30),
            'deposit_amount' => $this->faker->randomFloat(2, 10, 100),
            'required_products' => [],
            'is_package' => $this->faker->boolean(20),
            'package_services' => [],
            'online_booking_enabled' => $this->faker->boolean(80),
            'max_advance_booking_days' => $this->faker->numberBetween(7, 90),
            'preparation_instructions' => $this->faker->optional()->paragraph(),
            'aftercare_instructions' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'beauty'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
