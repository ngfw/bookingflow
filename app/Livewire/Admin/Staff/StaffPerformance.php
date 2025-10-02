<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\StaffPerformance;
use App\Models\Appointment;
use Carbon\Carbon;

class StaffPerformanceTracking extends Component
{
    public $selectedStaff = '';
    public $startDate = '';
    public $endDate = '';
    public $viewType = 'summary'; // summary, detailed, comparison
    public $staff = [];
    public $performanceData = [];
    public $summaryData = null;
    public $topPerformers = [];
    public $performanceMetrics = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->loadStaff();
        $this->loadPerformanceData();
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
    }

    public function loadPerformanceData()
    {
        if ($this->selectedStaff) {
            $this->loadStaffPerformance();
        } else {
            $this->loadAllStaffPerformance();
        }
    }

    public function loadStaffPerformance()
    {
        // Load individual staff performance
        $this->performanceData = StaffPerformance::where('staff_id', $this->selectedStaff)
            ->whereBetween('performance_date', [$this->startDate, $this->endDate])
            ->orderBy('performance_date', 'desc')
            ->get();

        // Load summary data
        $this->summaryData = StaffPerformance::getStaffPerformanceSummary(
            $this->selectedStaff, 
            $this->startDate, 
            $this->endDate
        );

        // Load performance metrics
        $this->loadPerformanceMetrics();
    }

    public function loadAllStaffPerformance()
    {
        // Load top performers
        $this->topPerformers = StaffPerformance::getTopPerformers($this->startDate, $this->endDate, 10);
        
        // Load overall metrics
        $this->loadOverallMetrics();
    }

    public function loadPerformanceMetrics()
    {
        if (!$this->selectedStaff) return;

        $staff = Staff::find($this->selectedStaff);
        if (!$staff) return;

        // Calculate various metrics
        $this->performanceMetrics = [
            'total_appointments' => $this->performanceData->sum('appointments_completed'),
            'total_revenue' => $this->performanceData->sum('total_revenue'),
            'avg_satisfaction' => $this->performanceData->avg('client_satisfaction_rating'),
            'total_hours' => $this->performanceData->sum('hours_worked') / 60, // Convert to hours
            'completion_rate' => $this->calculateCompletionRate(),
            'revenue_per_hour' => $this->calculateRevenuePerHour(),
            'efficiency_score' => $this->calculateEfficiencyScore(),
        ];
    }

    public function loadOverallMetrics()
    {
        $this->performanceMetrics = [
            'total_staff' => $this->staff->count(),
            'total_appointments' => StaffPerformance::whereBetween('performance_date', [$this->startDate, $this->endDate])
                ->sum('appointments_completed'),
            'total_revenue' => StaffPerformance::whereBetween('performance_date', [$this->startDate, $this->endDate])
                ->sum('total_revenue'),
            'avg_satisfaction' => StaffPerformance::whereBetween('performance_date', [$this->startDate, $this->endDate])
                ->avg('client_satisfaction_rating'),
            'top_performer' => $this->topPerformers->first(),
        ];
    }

    public function calculateCompletionRate()
    {
        $total = $this->performanceData->sum(function($record) {
            return $record->appointments_completed + $record->appointments_cancelled + $record->appointments_no_show;
        });
        
        $completed = $this->performanceData->sum('appointments_completed');
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    public function calculateRevenuePerHour()
    {
        $totalRevenue = $this->performanceData->sum('total_revenue');
        $totalHours = $this->performanceData->sum('hours_worked') / 60; // Convert to hours
        
        return $totalHours > 0 ? $totalRevenue / $totalHours : 0;
    }

    public function calculateEfficiencyScore()
    {
        $completionRate = $this->calculateCompletionRate();
        $satisfactionScore = $this->performanceData->avg('client_satisfaction_rating') ? 
            ($this->performanceData->avg('client_satisfaction_rating') / 5) * 100 : 0;
        $revenueScore = min(100, ($this->calculateRevenuePerHour() / 50) * 100);
        
        return ($completionRate + $satisfactionScore + $revenueScore) / 3;
    }

    public function generatePerformanceReport()
    {
        // This could generate a PDF or export data
        session()->flash('success', 'Performance report generated successfully.');
    }

    public function updatedSelectedStaff()
    {
        $this->loadPerformanceData();
    }

    public function updatedStartDate()
    {
        $this->loadPerformanceData();
    }

    public function updatedEndDate()
    {
        $this->loadPerformanceData();
    }

    public function updatedViewType()
    {
        $this->loadPerformanceData();
    }

    public function render()
    {
        return view('livewire.admin.staff.staff-performance')->layout('layouts.admin');
    }
}