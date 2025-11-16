<?php

namespace Database\Factories;

use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembershipTierFactory extends Factory
{
    protected $model = MembershipTier::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['fas fa-medal', 'fas fa-crown', 'fas fa-star']),
            'min_points' => $this->faker->numberBetween(0, 1000),
            'max_points' => $this->faker->optional()->numberBetween(1000, 5000),
            'min_spent' => $this->faker->randomFloat(2, 0, 1000),
            'max_spent' => $this->faker->optional()->randomFloat(2, 1000, 10000),
            'min_visits' => $this->faker->numberBetween(0, 10),
            'max_visits' => $this->faker->optional()->numberBetween(10, 100),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 20),
            'discount_amount' => $this->faker->randomFloat(2, 0, 100),
            'bonus_points_multiplier' => $this->faker->randomFloat(2, 1.0, 3.0),
            'free_shipping' => $this->faker->boolean(),
            'priority_booking' => $this->faker->boolean(),
            'exclusive_services' => $this->faker->boolean(),
            'birthday_bonus' => $this->faker->boolean(),
            'anniversary_bonus' => $this->faker->boolean(),
            'benefits' => ['Benefit 1', 'Benefit 2'],
            'restrictions' => [],
            'is_active' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
