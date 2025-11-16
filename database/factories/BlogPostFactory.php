<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => $this->faker->optional()->imageUrl(1200, 630, 'business'),
            'meta_data' => [
                'meta_title' => $title,
                'meta_description' => $this->faker->sentence(),
            ],
            'author_name' => $this->faker->name(),
            'author_email' => $this->faker->email(),
            'tags' => $this->faker->words(3),
            'category' => $this->faker->randomElement(['Beauty Tips', 'Hair Care', 'Skin Care', 'News', 'Promotions']),
            'is_published' => $this->faker->boolean(80),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'likes_count' => $this->faker->numberBetween(0, 100),
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
