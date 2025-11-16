<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'label' => $this->faker->words(2, true),
            'url' => $this->faker->url(),
            'icon' => $this->faker->optional()->randomElement(['fas fa-home', 'fas fa-user', 'fas fa-calendar']),
            'parent_id' => null,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(90),
            'target' => $this->faker->randomElement(['_self', '_blank']),
            'classes' => $this->faker->optional()->word(),
        ];
    }
}
