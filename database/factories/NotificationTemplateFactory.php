<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'type' => $this->faker->randomElement(['email', 'sms', 'push']),
            'subject' => $this->faker->optional()->sentence(),
            'body' => $this->faker->paragraphs(3, true),
            'variables' => ['client_name', 'appointment_date'],
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
