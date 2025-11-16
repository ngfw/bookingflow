<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(5, true),
            'meta_data' => [
                'meta_title' => $title,
                'meta_description' => $this->faker->sentence(),
            ],
            'is_published' => $this->faker->boolean(80),
            'published_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
