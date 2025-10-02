<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\StaffPayroll;
use App\Models\StaffPerformance;
use Carbon\Carbon;

class StaffPayrollManagement extends Component
{
    public $staff = [];
    public $payrolls = [];
    public $selectedStaff = '';
    public $selectedPeriod = '';
    public $startDate = '';
    public $endDate = '';
    public $payPeriodType = 'monthly';
    public $showGenerateModal = false;
    public $showPayrollModal = false;
    public $selectedPayroll = null;

    // Summary statistics
    public $totalGrossPay = 0;
    public $totalNetPay = 0;
    public $totalDeductions = 0;
    public $payrollCount = 0;

    public function mount()
    {
        $this->loadStaff();
        $this->loadPayrolls();
        $this->calculateSummary();
        
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
    }

    public function loadPayrolls()
    {
        $query = StaffPayroll::with('staff.user');
        
        if ($this->selectedStaff) {
            $query->where('staff_id', $this->selectedStaff);
        }
        
        if ($this->selectedPeriod) {
            switch ($this->selectedPeriod) {
                case 'current_month':
                    $query->whereBetween('pay_period_start', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                    break;
                case 'last_month':
                    $query->whereBetween('pay_period_start', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ]);
                    break;
                case 'current_quarter':
                    $query->whereBetween('pay_period_start', [
                        Carbon::now()->startOfQuarter(),
                        Carbon::now()->endOfQuarter()
                    ]);
                    break;
                case 'current_year':
                    $query->whereBetween('pay_period_start', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear()
                    ]);
                    break;
            }
        }
        
        $this->payrolls = $query->orderBy('pay_period_start', 'desc')
            ->orderBy('staff_id')
            ->get();
    }

    public function calculateSummary()
    {
        $this->totalGrossPay = $this->payrolls->sum('gross_pay');
        $this->totalNetPay = $this->payrolls->sum('net_pay');
        $this->totalDeductions = $this->payrolls->sum('total_deductions');
        $this->payrollCount = $this->payrolls->count();
    }

    public function updatedSelectedStaff()
    {
        $this->loadPayrolls();
        $this->calculateSummary();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadPayrolls();
        $this->calculateSummary();
    }

    public function showGeneratePayrollModal()
    {
        $this->showGenerateModal = true;
    }

    public function generatePayroll()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'payPeriodType' => 'required|in:weekly,bi_weekly,monthly',
        ]);

        try {
            $generatedPayrolls = StaffPayroll::generatePayrollForPeriod(
                $this->startDate,
                $this->endDate,
                $this->payPeriodType
            );

            if (count($generatedPayrolls) > 0) {
                session()->flash('success', "Successfully generated " . count($generatedPayrolls) . " payroll records.");
            } else {
                session()->flash('info', 'No new payroll records were generated. They may already exist for this period.');
            }

            $this->closeGenerateModal();
            $this->loadPayrolls();
            $this->calculateSummary();
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    public function viewPayroll($payrollId)
    {
        $this->selectedPayroll = StaffPayroll::with('staff.user')->findOrFail($payrollId);
        $this->showPayrollModal = true;
    }

    public function approvePayroll($payrollId)
    {
        $payroll = StaffPayroll::findOrFail($payrollId);
        $payroll->approve();
        
        session()->flash('success', 'Payroll approved successfully.');
        $this->loadPayrolls();
        $this->calculateSummary();
    }

    public function markAsPaid($payrollId)
    {
        $payroll = StaffPayroll::findOrFail($payrollId);
        $payroll->markAsPaid();
        
        session()->flash('success', 'Payroll marked as paid.');
        $this->loadPayrolls();
        $this->calculateSummary();
    }

    public function deletePayroll($payrollId)
    {
        $payroll = StaffPayroll::findOrFail($payrollId);
        
        if ($payroll->status === 'paid') {
            session()->flash('error', 'Cannot delete paid payroll records.');
            return;
        }
        
        $payroll->delete();
        session()->flash('success', 'Payroll deleted successfully.');
        $this->loadPayrolls();
        $this->calculateSummary();
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
    }

    public function closePayrollModal()
    {
        $this->showPayrollModal = false;
        $this->selectedPayroll = null;
    }

    public function exportPayroll($payrollId)
    {
        $payroll = StaffPayroll::with('staff.user')->findOrFail($payrollId);
        
        // Generate payroll summary data for export
        $payrollData = [
            'staff_name' => $payroll->staff->user->name,
            'pay_period' => $payroll->pay_period_display,
            'gross_pay' => $payroll->gross_pay,
            'net_pay' => $payroll->net_pay,
            'total_deductions' => $payroll->total_deductions,
            'status' => $payroll->status_display,
        ];
        
        // In a real application, you would generate a PDF or Excel file here
        session()->flash('success', 'Payroll export functionality would be implemented here.');
    }

    public function render()
    {
        return view('livewire.admin.staff.staff-payroll-management')->layout('layouts.admin');
    }
}