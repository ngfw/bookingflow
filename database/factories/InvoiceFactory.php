<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 500);
        $taxRate = $this->faker->randomFloat(2, 5, 10);
        $discountAmount = $this->faker->randomFloat(2, 0, 50);
        $taxAmount = ($subtotal - $discountAmount) * ($taxRate / 100);
        $totalAmount = $subtotal - $discountAmount + $taxAmount;
        $amountPaid = $this->faker->randomFloat(2, 0, $totalAmount);
        $balanceDue = $totalAmount - $amountPaid;

        return [
            'invoice_number' => 'INV' . $this->faker->unique()->numerify('######'),
            'client_id' => Client::factory(),
            'appointment_id' => Appointment::factory(),
            'created_by' => User::factory(),
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
            'invoice_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'paid_date' => $this->faker->optional()->dateTime(),
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'notes' => $this->faker->optional()->paragraph(),
            'terms_conditions' => $this->faker->optional()->paragraph(),
        ];
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'amount_paid' => $attributes['total_amount'],
                'balance_due' => 0,
                'paid_date' => $this->faker->dateTime(),
            ];
        });
    }
}
