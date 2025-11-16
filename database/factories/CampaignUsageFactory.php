<?php

namespace Database\Factories;

use App\Models\CampaignUsage;
use App\Models\PromotionalCampaign;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignUsageFactory extends Factory
{
    protected $model = CampaignUsage::class;

    public function definition(): array
    {
        $originalAmount = $this->faker->randomFloat(2, 50, 500);
        $discountAmount = $this->faker->randomFloat(2, 5, 50);
        $finalAmount = $originalAmount - $discountAmount;

        return [
            'campaign_id' => PromotionalCampaign::factory(),
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'invoice_id' => Invoice::factory(),
            'usage_type' => $this->faker->randomElement(['appointment', 'purchase', 'referral', 'manual']),
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'bonus_points_earned' => $this->faker->numberBetween(0, 100),
            'promo_code_used' => $this->faker->optional()->lexify('PROMO???'),
            'channel' => $this->faker->randomElement(['online', 'phone', 'walk_in', 'email']),
            'status' => $this->faker->randomElement(['completed', 'cancelled', 'refunded']),
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => [],
        ];
    }
}
