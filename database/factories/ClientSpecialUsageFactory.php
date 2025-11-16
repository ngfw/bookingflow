<?php

namespace Database\Factories;

use App\Models\ClientSpecialUsage;
use App\Models\BirthdayAnniversarySpecial;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientSpecialUsageFactory extends Factory
{
    protected $model = ClientSpecialUsage::class;

    public function definition(): array
    {
        $originalAmount = $this->faker->randomFloat(2, 50, 500);
        $discountAmount = $this->faker->randomFloat(2, 5, 50);
        $finalAmount = $originalAmount - $discountAmount;

        return [
            'special_id' => BirthdayAnniversarySpecial::factory(),
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'invoice_id' => Invoice::factory(),
            'event_type' => $this->faker->randomElement(['birthday', 'anniversary']),
            'event_date' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'special_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'bonus_points_earned' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['used', 'expired', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => [],
        ];
    }
}
