<?php

namespace Database\Factories;

use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignRecipientFactory extends Factory
{
    protected $model = CampaignRecipient::class;

    public function definition(): array
    {
        return [
            'campaign_id' => EmailCampaign::factory(),
            'client_id' => Client::factory(),
            'email' => $this->faker->email(),
            'status' => $this->faker->randomElement(['pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'unsubscribed']),
            'sent_at' => $this->faker->optional()->dateTime(),
            'delivered_at' => $this->faker->optional()->dateTime(),
            'opened_at' => $this->faker->optional()->dateTime(),
            'clicked_at' => $this->faker->optional()->dateTime(),
            'bounced_at' => $this->faker->optional()->dateTime(),
            'unsubscribed_at' => $this->faker->optional()->dateTime(),
            'error_message' => $this->faker->optional()->sentence(),
            'tracking_data' => [],
        ];
    }
}
