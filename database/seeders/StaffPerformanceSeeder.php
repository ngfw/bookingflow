<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\StaffPerformance;
use Carbon\Carbon;

class StaffPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::with('user')->get();
        
        if ($staff->isEmpty()) {
            $this->command->info('No staff found. Please run the basic data seeder first.');
            return;
        }

        // Generate performance data for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            foreach ($staff as $staffMember) {
                // Skip weekends for some staff
                if ($date->isWeekend() && rand(1, 3) === 1) {
                    continue;
                }

                // Generate realistic performance data
                $appointmentsCompleted = rand(3, 12);
                $appointmentsCancelled = rand(0, 2);
                $appointmentsNoShow = rand(0, 1);
                $totalRevenue = $appointmentsCompleted * rand(25, 85); // $25-$85 per appointment
                $hoursWorked = rand(360, 540); // 6-9 hours in minutes
                $satisfactionRating = rand(350, 500) / 100; // 3.5-5.0 rating

                StaffPerformance::updateOrCreate(
                    [
                        'staff_id' => $staffMember->id,
                        'performance_date' => $date->format('Y-m-d'),
                    ],
                    [
                        'appointments_completed' => $appointmentsCompleted,
                        'appointments_cancelled' => $appointmentsCancelled,
                        'appointments_no_show' => $appointmentsNoShow,
                        'total_revenue' => $totalRevenue,
                        'commission_earned' => $totalRevenue * 0.15, // 15% commission
                        'hours_worked' => $hoursWorked,
                        'overtime_hours' => $hoursWorked > 480 ? $hoursWorked - 480 : 0, // Overtime if > 8 hours
                        'client_satisfaction_rating' => $satisfactionRating,
                        'products_used' => rand(5, 15),
                        'product_cost' => rand(25, 75),
                        'notes' => $this->getRandomNotes(),
                        'performance_metrics' => [
                            'efficiency_score' => rand(75, 95),
                            'client_retention_rate' => rand(80, 95),
                            'upsell_success_rate' => rand(20, 40),
                        ],
                    ]
                );
            }
        }

        $this->command->info('Staff performance data seeded successfully!');
    }

    private function getRandomNotes()
    {
        $notes = [
            'Great day with high client satisfaction',
            'Busy day with many walk-ins',
            'Some clients were running late',
            'Excellent teamwork with other staff',
            'New client referrals came in',
            'Product demonstration went well',
            'Client requested follow-up appointment',
            'Training new staff member',
            'Equipment maintenance required',
            'Special event preparation',
        ];

        return $notes[array_rand($notes)];
    }
}