<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'image_path' => $this->faker->imageUrl(1200, 800, 'beauty'),
            'thumbnail_path' => $this->faker->imageUrl(400, 300, 'beauty'),
            'alt_text' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['Hair', 'Nails', 'Spa', 'Makeup', 'Treatments']),
            'tags' => $this->faker->words(3),
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_featured' => $this->faker->boolean(20),
            'is_published' => $this->faker->boolean(90),
            'views_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
