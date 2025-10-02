<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Referral;
use App\Models\LoyaltyPoint;
use Carbon\Carbon;

class ReferralSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::with('user')->get();
        
        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please run the basic data seeder first.');
            return;
        }

        // Create sample referrals for each client
        foreach ($clients as $client) {
            // Create pending referrals
            for ($i = 0; $i < rand(1, 3); $i++) {
                Referral::createReferral(
                    $client->id,
                    [
                        'email' => 'friend' . $i . '@example.com',
                        'name' => 'Friend ' . ($i + 1),
                        'phone' => '+123456789' . $i,
                    ],
                    [
                        'method' => ['code', 'link', 'manual', 'social_media'][rand(0, 3)],
                        'notes' => 'Referred through ' . ['word of mouth', 'social media', 'email', 'phone'][rand(0, 3)],
                        'expiry_date' => Carbon::now()->addMonths(rand(1, 6)),
                        'referrer_reward' => rand(5, 25),
                        'referred_reward' => rand(5, 15),
                        'referrer_points' => rand(50, 200),
                        'referred_points' => rand(25, 100),
                        'metadata' => [
                            'source' => ['facebook', 'instagram', 'twitter', 'email', 'phone'][rand(0, 4)],
                            'campaign' => 'summer_referral_2024',
                        ],
                    ]
                );
            }

            // Create completed referrals
            for ($i = 0; $i < rand(1, 2); $i++) {
                $referredClient = $clients->where('id', '!=', $client->id)->random();
                
                $referral = Referral::createReferral(
                    $client->id,
                    [
                        'email' => $referredClient->user->email,
                        'name' => $referredClient->user->name,
                        'phone' => '+123456789' . rand(0, 9),
                    ],
                    [
                        'method' => ['code', 'link', 'manual'][rand(0, 2)],
                        'notes' => 'Successfully referred new client',
                        'expiry_date' => Carbon::now()->addMonths(3),
                        'referrer_reward' => rand(10, 30),
                        'referred_reward' => rand(5, 20),
                        'referrer_points' => rand(100, 300),
                        'referred_points' => rand(50, 150),
                        'metadata' => [
                            'source' => ['facebook', 'instagram', 'email'][rand(0, 2)],
                            'campaign' => 'spring_referral_2024',
                        ],
                    ]
                );

                // Complete the referral
                try {
                    $referral->complete($referredClient->id);
                } catch (\Exception $e) {
                    // Skip if completion fails
                }
            }

            // Create expired referrals
            for ($i = 0; $i < rand(0, 2); $i++) {
                $expiredDate = Carbon::now()->subDays(rand(1, 30));
                $referral = Referral::createReferral(
                    $client->id,
                    [
                        'email' => 'expired' . $i . '@example.com',
                        'name' => 'Expired Referral ' . ($i + 1),
                        'phone' => '+123456789' . $i,
                    ],
                    [
                        'method' => ['code', 'link'][rand(0, 1)],
                        'notes' => 'Referral expired without completion',
                        'expiry_date' => $expiredDate,
                        'referrer_reward' => rand(5, 20),
                        'referred_reward' => rand(5, 15),
                        'referrer_points' => rand(50, 150),
                        'referred_points' => rand(25, 100),
                        'metadata' => [
                            'source' => ['email', 'phone'][rand(0, 1)],
                            'campaign' => 'winter_referral_2023',
                        ],
                    ]
                );

                // Expire the referral
                $referral->expire();
            }

            // Create cancelled referrals
            for ($i = 0; $i < rand(0, 1); $i++) {
                $referral = Referral::createReferral(
                    $client->id,
                    [
                        'email' => 'cancelled' . $i . '@example.com',
                        'name' => 'Cancelled Referral ' . ($i + 1),
                        'phone' => '+123456789' . $i,
                    ],
                    [
                        'method' => ['code', 'manual'][rand(0, 1)],
                        'notes' => 'Referral cancelled by client',
                        'expiry_date' => Carbon::now()->addMonths(2),
                        'referrer_reward' => rand(5, 15),
                        'referred_reward' => rand(5, 10),
                        'referrer_points' => rand(50, 100),
                        'referred_points' => rand(25, 75),
                        'metadata' => [
                            'source' => ['email', 'phone'][rand(0, 1)],
                            'campaign' => 'fall_referral_2023',
                        ],
                    ]
                );

                // Cancel the referral
                $referral->cancel('Client declined to sign up');
            }
        }

        // Create some referrals expiring soon
        foreach ($clients->take(3) as $client) {
            $expiringDate = Carbon::now()->addDays(rand(1, 7));
            Referral::createReferral(
                $client->id,
                [
                    'email' => 'expiring' . rand(1, 100) . '@example.com',
                    'name' => 'Expiring Referral',
                    'phone' => '+123456789' . rand(0, 9),
                ],
                [
                    'method' => 'code',
                    'notes' => 'Referral expiring soon',
                    'expiry_date' => $expiringDate,
                    'referrer_reward' => rand(10, 25),
                    'referred_reward' => rand(5, 15),
                    'referrer_points' => rand(100, 200),
                    'referred_points' => rand(50, 100),
                    'metadata' => [
                        'source' => 'email',
                        'campaign' => 'urgent_referral_2024',
                    ],
                ]
            );
        }

        $this->command->info('Referral data seeded successfully!');
    }
}