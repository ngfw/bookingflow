<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\StaffPayroll;
use App\Models\StaffPerformance;
use Carbon\Carbon;

class StaffPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::with('user')->get();
        
        if ($staff->isEmpty()) {
            $this->command->info('No staff found. Please run the basic data seeder first.');
            return;
        }

        // Generate payroll for the last 3 months
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($staff as $staffMember) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                $periodStart = $currentDate->copy()->startOfMonth();
                $periodEnd = $currentDate->copy()->endOfMonth();
                
                // Check if payroll already exists for this period
                $existingPayroll = StaffPayroll::where('staff_id', $staffMember->id)
                    ->where('pay_period_start', $periodStart)
                    ->where('pay_period_end', $periodEnd)
                    ->first();

                if (!$existingPayroll) {
                    // Get performance data for the period
                    $performanceData = StaffPerformance::where('staff_id', $staffMember->id)
                        ->whereBetween('performance_date', [$periodStart, $periodEnd])
                        ->get();

                    // Calculate payroll
                    $payroll = $this->calculateStaffPayroll($staffMember, $performanceData, $periodStart, $periodEnd);
                    
                    if ($payroll) {
                        $this->command->info("Generated payroll for {$staffMember->user->name} for {$periodStart->format('M Y')}");
                    }
                }
                
                $currentDate->addMonth();
            }
        }

        $this->command->info('Staff payroll data seeded successfully!');
    }

    private function calculateStaffPayroll($staff, $performanceData, $startDate, $endDate)
    {
        // Calculate hours worked
        $totalHours = $performanceData->sum('hours_worked');
        $overtimeHours = $performanceData->sum('overtime_hours');
        $regularHours = $totalHours - $overtimeHours;

        // Get staff rates (randomize for demo)
        $hourlyRate = rand(15, 25) + (rand(0, 99) / 100); // $15-25 per hour
        $overtimeRate = $hourlyRate * 1.5; // 1.5x for overtime

        // Calculate regular and overtime pay
        $regularPay = ($regularHours / 60) * $hourlyRate;
        $overtimePay = ($overtimeHours / 60) * $overtimeRate;

        // Calculate commission
        $commissionEarned = $performanceData->sum('commission_earned');

        // Calculate gross pay
        $grossPay = $regularPay + $overtimePay + $commissionEarned;

        // Calculate deductions (simplified tax calculation)
        $taxRate = 0.22; // 22% federal tax
        $socialSecurityRate = 0.062; // 6.2% social security
        $medicareRate = 0.0145; // 1.45% medicare

        $taxDeduction = $grossPay * $taxRate;
        $socialSecurity = $grossPay * $socialSecurityRate;
        $medicare = $grossPay * $medicareRate;
        $healthInsurance = 150.00; // Fixed monthly health insurance
        $retirementContribution = $grossPay * 0.05; // 5% retirement contribution
        $otherDeductions = 0.00;

        $totalDeductions = $taxDeduction + $socialSecurity + $medicare + 
                          $healthInsurance + $retirementContribution + $otherDeductions;

        $netPay = $grossPay - $totalDeductions;

        // Determine status based on date
        $status = 'calculated';
        if ($endDate->lt(Carbon::now()->subDays(7))) {
            $status = 'approved';
        }
        if ($endDate->lt(Carbon::now()->subDays(14))) {
            $status = 'paid';
        }

        // Create payroll record
        return StaffPayroll::create([
            'staff_id' => $staff->id,
            'pay_period_start' => $startDate,
            'pay_period_end' => $endDate,
            'pay_period_type' => 'monthly',
            'hours_worked' => $regularHours,
            'overtime_hours' => $overtimeHours,
            'hourly_rate' => $hourlyRate,
            'overtime_rate' => $overtimeRate,
            'regular_pay' => $regularPay,
            'overtime_pay' => $overtimePay,
            'commission_earned' => $commissionEarned,
            'bonus_amount' => 0.00,
            'gross_pay' => $grossPay,
            'tax_deduction' => $taxDeduction,
            'social_security' => $socialSecurity,
            'medicare' => $medicare,
            'health_insurance' => $healthInsurance,
            'retirement_contribution' => $retirementContribution,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'status' => $status,
            'pay_date' => $status === 'paid' ? $endDate->copy()->addDays(rand(1, 5)) : null,
            'payroll_details' => [
                'appointments_completed' => $performanceData->sum('appointments_completed'),
                'total_revenue' => $performanceData->sum('total_revenue'),
                'avg_satisfaction' => $performanceData->avg('client_satisfaction_score'),
            ],
        ]);
    }
}