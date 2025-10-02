<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Service;
use App\Models\StaffCommissionSetting;
use Carbon\Carbon;

class StaffCommissionSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::with('user')->get();
        $services = Service::where('is_active', true)->get();
        
        if ($staff->isEmpty()) {
            $this->command->info('No staff found. Please run the basic data seeder first.');
            return;
        }

        foreach ($staff as $staffMember) {
            // Create general commission setting for all services
            StaffCommissionSetting::updateOrCreate(
                [
                    'staff_id' => $staffMember->id,
                    'service_id' => null,
                ],
                [
                    'commission_type' => 'percentage',
                    'commission_rate' => 15.00, // 15% commission
                    'minimum_threshold' => 0.00,
                    'maximum_cap' => 2000.00, // Max $2000 per month
                    'calculation_basis' => 'revenue',
                    'payment_frequency' => 'monthly',
                    'is_active' => true,
                    'effective_date' => Carbon::now()->subDays(30),
                    'notes' => 'General commission rate for all services',
                ]
            );

            // Create service-specific commission settings for some services
            if ($services->count() > 0) {
                $premiumServices = $services->take(2); // First 2 services get premium rates
                
                foreach ($premiumServices as $service) {
                    StaffCommissionSetting::updateOrCreate(
                        [
                            'staff_id' => $staffMember->id,
                            'service_id' => $service->id,
                        ],
                        [
                            'commission_type' => 'percentage',
                            'commission_rate' => 20.00, // 20% for premium services
                            'minimum_threshold' => 50.00,
                            'maximum_cap' => 3000.00, // Higher cap for premium services
                            'calculation_basis' => 'revenue',
                            'payment_frequency' => 'monthly',
                            'is_active' => true,
                            'effective_date' => Carbon::now()->subDays(30),
                            'notes' => "Premium commission rate for {$service->name}",
                        ]
                    );
                }

                // Create a tiered commission structure for one service
                if ($services->count() > 2) {
                    $tieredService = $services->skip(2)->first();
                    
                    StaffCommissionSetting::updateOrCreate(
                        [
                            'staff_id' => $staffMember->id,
                            'service_id' => $tieredService->id,
                        ],
                        [
                            'commission_type' => 'tiered',
                            'tiered_rates' => [
                                ['min' => 0, 'max' => 1000, 'rate' => 10],      // 10% for first $1000
                                ['min' => 1000, 'max' => 3000, 'rate' => 15],   // 15% for $1000-$3000
                                ['min' => 3000, 'max' => 999999, 'rate' => 20], // 20% for above $3000
                            ],
                            'minimum_threshold' => 0.00,
                            'maximum_cap' => 5000.00,
                            'calculation_basis' => 'revenue',
                            'payment_frequency' => 'monthly',
                            'is_active' => true,
                            'effective_date' => Carbon::now()->subDays(30),
                            'notes' => "Tiered commission structure for {$tieredService->name}",
                        ]
                    );
                }
            }

            // Create a fixed commission setting for one staff member
            if ($staffMember->id === $staff->first()->id) {
                StaffCommissionSetting::updateOrCreate(
                    [
                        'staff_id' => $staffMember->id,
                        'service_id' => null,
                        'commission_type' => 'fixed',
                    ],
                    [
                        'commission_type' => 'fixed',
                        'fixed_amount' => 25.00, // $25 per appointment
                        'minimum_threshold' => 0.00,
                        'maximum_cap' => 1500.00, // Max $1500 per month
                        'calculation_basis' => 'appointments',
                        'payment_frequency' => 'weekly',
                        'is_active' => false, // Inactive by default
                        'effective_date' => Carbon::now()->addDays(30),
                        'expiry_date' => Carbon::now()->addDays(90),
                        'notes' => 'Alternative fixed commission structure (inactive)',
                    ]
                );
            }
        }

        $this->command->info('Staff commission settings seeded successfully!');
    }
}