<?php

namespace Database\Factories;

use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchiseFactory extends Factory
{
    protected $model = Franchise::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Franchise',
            'code' => strtoupper($this->faker->unique()->lexify('FR???')),
            'owner_name' => $this->faker->name(),
            'owner_email' => $this->faker->companyEmail(),
            'owner_phone' => $this->faker->phoneNumber(),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'timezone' => $this->faker->timezone(),
            'currency' => $this->faker->currencyCode(),
            'start_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('+1 year', '+5 years'),
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended', 'terminated']),
            'franchise_fee' => $this->faker->randomFloat(2, 10000, 100000),
            'royalty_percentage' => $this->faker->randomFloat(2, 5, 15),
            'marketing_fee_percentage' => $this->faker->randomFloat(2, 1, 5),
            'settings' => [],
            'metadata' => [],
        ];
    }
}
