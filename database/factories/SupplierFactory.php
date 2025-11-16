<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'website' => $this->faker->optional()->url(),
            'tax_id' => $this->faker->optional()->numerify('TAX######'),
            'payment_terms' => $this->faker->optional()->randomElement(['Net 15', 'Net 30', 'Net 60', 'COD']),
            'is_active' => $this->faker->boolean(90),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
