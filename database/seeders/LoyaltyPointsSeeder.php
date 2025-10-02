<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\LoyaltyPoint;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;

class LoyaltyPointsSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::with('user')->get();
        
        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please run the basic data seeder first.');
            return;
        }

        // Create sample loyalty points for each client
        foreach ($clients as $client) {
            // Earn points from appointments
            for ($i = 0; $i < rand(3, 8); $i++) {
                $appointmentDate = Carbon::now()->subDays(rand(1, 90));
                $serviceValue = rand(50, 200) + (rand(0, 99) / 100);
                $pointsEarned = floor($serviceValue * 1.5); // 1.5 points per dollar
                
                LoyaltyPoint::earnPoints(
                    $client->id,
                    $pointsEarned,
                    'appointment',
                    [
                        'description' => 'Points earned from appointment',
                        'transaction_value' => $serviceValue,
                        'points_per_dollar' => 1.5,
                        'expiry_date' => $appointmentDate->addYear(),
                        'metadata' => [
                            'service_type' => 'Haircut',
                            'staff_name' => 'Jane Smith',
                            'appointment_date' => $appointmentDate->format('Y-m-d'),
                        ],
                    ]
                );
            }

            // Earn points from purchases
            for ($i = 0; $i < rand(1, 3); $i++) {
                $purchaseDate = Carbon::now()->subDays(rand(1, 60));
                $purchaseValue = rand(25, 100) + (rand(0, 99) / 100);
                $pointsEarned = floor($purchaseValue * 1.0); // 1 point per dollar for purchases
                
                LoyaltyPoint::earnPoints(
                    $client->id,
                    $pointsEarned,
                    'purchase',
                    [
                        'description' => 'Points earned from product purchase',
                        'transaction_value' => $purchaseValue,
                        'points_per_dollar' => 1.0,
                        'expiry_date' => $purchaseDate->addYear(),
                        'metadata' => [
                            'product_name' => 'Shampoo',
                            'quantity' => 1,
                            'purchase_date' => $purchaseDate->format('Y-m-d'),
                        ],
                    ]
                );
            }

            // Earn referral points
            if (rand(0, 1)) {
                $referralDate = Carbon::now()->subDays(rand(1, 30));
                LoyaltyPoint::earnPoints(
                    $client->id,
                    100,
                    'referral',
                    [
                        'description' => 'Referral bonus - new client signed up',
                        'expiry_date' => $referralDate->addYear(),
                        'metadata' => [
                            'referred_client' => 'New Client',
                            'referral_date' => $referralDate->format('Y-m-d'),
                        ],
                    ]
                );
            }

            // Earn birthday bonus
            if (rand(0, 1)) {
                $birthdayDate = Carbon::now()->subDays(rand(1, 365));
                LoyaltyPoint::earnPoints(
                    $client->id,
                    50,
                    'birthday',
                    [
                        'description' => 'Birthday bonus points',
                        'expiry_date' => $birthdayDate->addYear(),
                        'metadata' => [
                            'birthday_date' => $birthdayDate->format('Y-m-d'),
                            'bonus_type' => 'birthday',
                        ],
                    ]
                );
            }

            // Earn review bonus
            if (rand(0, 1)) {
                $reviewDate = Carbon::now()->subDays(rand(1, 45));
                LoyaltyPoint::earnPoints(
                    $client->id,
                    25,
                    'review',
                    [
                        'description' => 'Review bonus - 5-star review',
                        'expiry_date' => $reviewDate->addYear(),
                        'metadata' => [
                            'review_rating' => 5,
                            'review_date' => $reviewDate->format('Y-m-d'),
                        ],
                    ]
                );
            }

            // Redeem some points
            $currentBalance = LoyaltyPoint::getClientBalance($client->id);
            if ($currentBalance > 100) {
                $pointsToRedeem = rand(50, min(200, $currentBalance));
                LoyaltyPoint::redeemPoints(
                    $client->id,
                    $pointsToRedeem,
                    'Points redeemed for discount',
                    [
                        'transaction_value' => $pointsToRedeem * 0.01, // $0.01 per point
                        'metadata' => [
                            'redemption_type' => 'discount',
                            'discount_amount' => $pointsToRedeem * 0.01,
                        ],
                    ]
                );
            }
        }

        // Create some expired points
        foreach ($clients->take(2) as $client) {
            $expiredDate = Carbon::now()->subDays(rand(1, 30));
            LoyaltyPoint::create([
                'client_id' => $client->id,
                'transaction_type' => 'earned',
                'points' => 50,
                'source' => 'appointment',
                'description' => 'Expired points from old appointment',
                'transaction_value' => 100.00,
                'points_per_dollar' => 0.5,
                'expiry_date' => $expiredDate,
                'is_expired' => true,
                'created_at' => $expiredDate->subYear(),
                'updated_at' => $expiredDate->subYear(),
            ]);
        }

        // Create some points expiring soon
        foreach ($clients->take(3) as $client) {
            $expiringDate = Carbon::now()->addDays(rand(1, 30));
            LoyaltyPoint::create([
                'client_id' => $client->id,
                'transaction_type' => 'earned',
                'points' => 75,
                'source' => 'appointment',
                'description' => 'Points expiring soon',
                'transaction_value' => 150.00,
                'points_per_dollar' => 0.5,
                'expiry_date' => $expiringDate,
                'is_expired' => false,
                'created_at' => $expiringDate->subYear(),
                'updated_at' => $expiringDate->subYear(),
            ]);
        }

        $this->command->info('Loyalty points data seeded successfully!');
    }
}