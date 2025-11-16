<?php

namespace Database\Factories;

use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailCampaignFactory extends Factory
{
    protected $model = EmailCampaign::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(3, true),
            'created_by' => User::factory(),
            'status' => $this->faker->randomElement(['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled']),
            'target_criteria' => [],
            'scheduled_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'sent_at' => $this->faker->optional()->dateTime(),
            'total_recipients' => $this->faker->numberBetween(0, 1000),
            'total_sent' => $this->faker->numberBetween(0, 1000),
            'total_delivered' => $this->faker->numberBetween(0, 900),
            'total_opened' => $this->faker->numberBetween(0, 500),
            'total_clicked' => $this->faker->numberBetween(0, 200),
            'total_bounced' => $this->faker->numberBetween(0, 100),
            'total_unsubscribed' => $this->faker->numberBetween(0, 50),
            'metadata' => [],
        ];
    }
}
