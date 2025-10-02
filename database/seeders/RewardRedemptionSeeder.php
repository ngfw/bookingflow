<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\RewardRedemption;
use App\Models\LoyaltyPoint;
use Carbon\Carbon;

class RewardRedemptionSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::with('user')->get();
        
        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please run the basic data seeder first.');
            return;
        }

        // Create sample reward redemptions for each client
        foreach ($clients as $client) {
            $clientBalance = LoyaltyPoint::getClientBalance($client->id);
            
            if ($clientBalance >= 100) {
                // Create a discount reward
                RewardRedemption::createReward(
                    $client->id,
                    'discount',
                    '10% Off Next Service',
                    100,
                    [
                        'description' => 'Get 10% off your next service',
                        'discount_percentage' => 10.00,
                        'expiry_date' => Carbon::now()->addMonths(3),
                        'metadata' => [
                            'service_type' => 'any',
                            'max_discount' => 50.00,
                        ],
                    ]
                );
            }

            if ($clientBalance >= 50) {
                // Create a product reward
                RewardRedemption::createReward(
                    $client->id,
                    'product',
                    'Free Shampoo',
                    50,
                    [
                        'description' => 'Free professional shampoo (250ml)',
                        'cash_value' => 15.00,
                        'expiry_date' => Carbon::now()->addMonths(6),
                        'metadata' => [
                            'product_name' => 'Professional Shampoo',
                            'size' => '250ml',
                            'brand' => 'Salon Pro',
                        ],
                    ]
                );
            }

            if ($clientBalance >= 200) {
                // Create a service reward
                RewardRedemption::createReward(
                    $client->id,
                    'service',
                    'Free Consultation',
                    200,
                    [
                        'description' => 'Free 30-minute consultation with our stylist',
                        'cash_value' => 25.00,
                        'expiry_date' => Carbon::now()->addMonths(2),
                        'metadata' => [
                            'service_name' => 'Consultation',
                            'duration' => '30 minutes',
                            'staff_type' => 'senior_stylist',
                        ],
                    ]
                );
            }

            if ($clientBalance >= 500) {
                // Create a cash back reward
                RewardRedemption::createReward(
                    $client->id,
                    'cash_back',
                    '$10 Cash Back',
                    500,
                    [
                        'description' => 'Get $10 cash back on your account',
                        'cash_value' => 10.00,
                        'expiry_date' => Carbon::now()->addYear(),
                        'metadata' => [
                            'payment_method' => 'account_credit',
                            'minimum_purchase' => 50.00,
                        ],
                    ]
                );
            }
        }

        // Approve some rewards
        $pendingRewards = RewardRedemption::where('status', 'pending')->take(3)->get();
        foreach ($pendingRewards as $reward) {
            $reward->approve();
        }

        // Redeem some rewards
        $approvedRewards = RewardRedemption::where('status', 'approved')->take(2)->get();
        foreach ($approvedRewards as $reward) {
            try {
                $reward->redeem(1, null, null, 'Redeemed during appointment'); // Assuming staff ID 1 exists
            } catch (\Exception $e) {
                // Skip if redemption fails
            }
        }

        // Create some expired rewards
        foreach ($clients->take(2) as $client) {
            $expiredDate = Carbon::now()->subDays(rand(1, 30));
            RewardRedemption::create([
                'client_id' => $client->id,
                'reward_type' => 'discount',
                'reward_name' => 'Expired Discount',
                'description' => 'Expired discount reward',
                'points_required' => 75,
                'discount_percentage' => 15.00,
                'status' => 'expired',
                'redemption_code' => RewardRedemption::generateRedemptionCode(),
                'expiry_date' => $expiredDate,
                'created_at' => $expiredDate->subMonths(3),
                'updated_at' => $expiredDate->subMonths(3),
            ]);
        }

        // Create some rewards expiring soon
        foreach ($clients->take(3) as $client) {
            $expiringDate = Carbon::now()->addDays(rand(1, 7));
            RewardRedemption::create([
                'client_id' => $client->id,
                'reward_type' => 'product',
                'reward_name' => 'Expiring Product Reward',
                'description' => 'Product reward expiring soon',
                'points_required' => 80,
                'cash_value' => 12.00,
                'status' => 'approved',
                'redemption_code' => RewardRedemption::generateRedemptionCode(),
                'expiry_date' => $expiringDate,
                'created_at' => $expiringDate->subMonths(2),
                'updated_at' => $expiringDate->subMonths(2),
            ]);
        }

        $this->command->info('Reward redemption data seeded successfully!');
    }
}