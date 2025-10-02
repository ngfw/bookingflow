<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StaffPayroll extends Model
{
    use HasFactory;

    protected $table = 'staff_payroll';

    protected $fillable = [
        'staff_id',
        'pay_period_start',
        'pay_period_end',
        'pay_period_type',
        'hours_worked',
        'overtime_hours',
        'hourly_rate',
        'overtime_rate',
        'regular_pay',
        'overtime_pay',
        'commission_earned',
        'bonus_amount',
        'gross_pay',
        'tax_deduction',
        'social_security',
        'medicare',
        'health_insurance',
        'retirement_contribution',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'status',
        'pay_date',
        'notes',
        'payroll_details',
    ];

    protected function casts(): array
    {
        return [
            'pay_period_start' => 'date',
            'pay_period_end' => 'date',
            'hourly_rate' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'regular_pay' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'commission_earned' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'tax_deduction' => 'decimal:2',
            'social_security' => 'decimal:2',
            'medicare' => 'decimal:2',
            'health_insurance' => 'decimal:2',
            'retirement_contribution' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'pay_date' => 'date',
            'payroll_details' => 'array',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // Helper methods for payroll calculations
    public function calculateRegularPay()
    {
        $hours = $this->hours_worked / 60; // Convert minutes to hours
        return $hours * $this->hourly_rate;
    }

    public function calculateOvertimePay()
    {
        $hours = $this->overtime_hours / 60; // Convert minutes to hours
        return $hours * $this->overtime_rate;
    }

    public function calculateGrossPay()
    {
        return $this->regular_pay + $this->overtime_pay + $this->commission_earned + $this->bonus_amount;
    }

    public function calculateTotalDeductions()
    {
        return $this->tax_deduction + $this->social_security + $this->medicare + 
               $this->health_insurance + $this->retirement_contribution + $this->other_deductions;
    }

    public function calculateNetPay()
    {
        return $this->gross_pay - $this->total_deductions;
    }

    public function getTotalHoursAttribute()
    {
        return ($this->hours_worked + $this->overtime_hours) / 60; // Convert to hours
    }

    public function getRegularHoursAttribute()
    {
        return $this->hours_worked / 60; // Convert to hours
    }

    public function getOvertimeHoursAttribute()
    {
        return $this->overtime_hours / 60; // Convert to hours
    }

    public function getPayPeriodDisplayAttribute()
    {
        $start = $this->pay_period_start->format('M j');
        $end = $this->pay_period_end->format('M j, Y');
        return "{$start} - {$end}";
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'calculated' => 'Calculated',
            'approved' => 'Approved',
            'paid' => 'Paid',
            default => 'Unknown'
        };
    }

    public function getPayPeriodTypeDisplayAttribute()
    {
        return match($this->pay_period_type) {
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            default => 'Unknown'
        };
    }

    // Static methods for payroll management
    public static function generatePayrollForPeriod($startDate, $endDate, $payPeriodType = 'monthly')
    {
        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        $generatedPayrolls = [];

        foreach ($staff as $staffMember) {
            // Check if payroll already exists for this period
            $existingPayroll = self::where('staff_id', $staffMember->id)
                ->where('pay_period_start', $startDate)
                ->where('pay_period_end', $endDate)
                ->first();

            if ($existingPayroll) {
                continue; // Skip if already exists
            }

            // Get performance data for the period
            $performanceData = StaffPerformance::where('staff_id', $staffMember->id)
                ->whereBetween('performance_date', [$startDate, $endDate])
                ->get();

            // Calculate payroll
            $payroll = self::calculateStaffPayroll($staffMember, $performanceData, $startDate, $endDate, $payPeriodType);
            $generatedPayrolls[] = $payroll;
        }

        return $generatedPayrolls;
    }

    public static function calculateStaffPayroll($staff, $performanceData, $startDate, $endDate, $payPeriodType)
    {
        // Calculate hours worked
        $totalHours = $performanceData->sum('hours_worked');
        $overtimeHours = $performanceData->sum('overtime_hours');
        $regularHours = $totalHours - $overtimeHours;

        // Get staff rates
        $hourlyRate = $staff->hourly_rate ?? 0;
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

        // Create payroll record
        return self::create([
            'staff_id' => $staff->id,
            'pay_period_start' => $startDate,
            'pay_period_end' => $endDate,
            'pay_period_type' => $payPeriodType,
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
            'status' => 'calculated',
            'payroll_details' => [
                'appointments_completed' => $performanceData->sum('appointments_completed'),
                'total_revenue' => $performanceData->sum('total_revenue'),
                'avg_satisfaction' => $performanceData->avg('client_satisfaction_rating'),
            ],
        ]);
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function markAsPaid($payDate = null)
    {
        $this->update([
            'status' => 'paid',
            'pay_date' => $payDate ?? now(),
        ]);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isApproved()
    {
        return in_array($this->status, ['approved', 'paid']);
    }
}