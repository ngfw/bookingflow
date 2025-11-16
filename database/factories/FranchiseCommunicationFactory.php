<?php

namespace Database\Factories;

use App\Models\FranchiseCommunication;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchiseCommunicationFactory extends Factory
{
    protected $model = FranchiseCommunication::class;

    public function definition(): array
    {
        return [
            'franchise_id' => Franchise::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['email', 'phone', 'meeting', 'document', 'other']),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['sent', 'received', 'read', 'replied']),
            'direction' => $this->faker->randomElement(['inbound', 'outbound']),
            'metadata' => [],
        ];
    }
}
