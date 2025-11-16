<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_number' => 'PAY' . $this->faker->unique()->numerify('######'),
            'invoice_id' => Invoice::factory(),
            'client_id' => Client::factory(),
            'processed_by' => User::factory(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'bank_transfer', 'digital_wallet', 'check', 'other']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded', 'cancelled']),
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'notes' => $this->faker->optional()->sentence(),
            'reference_number' => $this->faker->optional()->numerify('REF######'),
            'transaction_id' => $this->faker->optional()->uuid(),
            'payment_details' => [
                'card_last_four' => $this->faker->optional()->numerify('####'),
                'card_type' => $this->faker->optional()->randomElement(['Visa', 'Mastercard', 'Amex']),
            ],
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
