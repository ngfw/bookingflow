<?php

namespace Database\Factories;

use App\Models\Testimonial;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'client_name' => $this->faker->name(),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->optional()->sentence(),
            'message' => $this->faker->paragraph(),
            'is_approved' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(20),
            'approved_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
