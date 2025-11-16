<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Salon',
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'description' => $this->faker->optional()->paragraph(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->optional()->url(),
            'business_hours' => [
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '10:00', 'close' => '16:00'],
            ],
            'amenities' => $this->faker->randomElements(['WiFi', 'Parking', 'Coffee', 'Magazines', 'Kids Area'], 3),
            'timezone' => $this->faker->timezone(),
            'tax_rate' => $this->faker->randomFloat(2, 5, 10),
            'currency' => $this->faker->currencyCode(),
            'is_active' => $this->faker->boolean(90),
            'is_headquarters' => false,
            'max_staff' => $this->faker->numberBetween(5, 50),
            'max_clients_per_day' => $this->faker->numberBetween(20, 100),
            'settings' => [],
        ];
    }

    public function headquarters(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_headquarters' => true,
        ]);
    }
}
