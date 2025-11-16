<?php

namespace Database\Factories;

use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactSubmissionFactory extends Factory
{
    protected $model = ContactSubmission::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['new', 'read', 'replied']),
            'read_at' => $this->faker->optional()->dateTime(),
            'replied_at' => $this->faker->optional()->dateTime(),
            'admin_notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
