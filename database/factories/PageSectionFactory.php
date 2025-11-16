<?php

namespace Database\Factories;

use App\Models\PageSection;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageSectionFactory extends Factory
{
    protected $model = PageSection::class;

    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'section_type' => $this->faker->randomElement(['text', 'image', 'video', 'gallery']),
            'settings' => [],
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
