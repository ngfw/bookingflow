<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\StaffPerformance;
use App\Models\Appointment;
use Carbon\Carbon;

class MobileStaffPerformance extends Component
{
    public $selectedStaff = '';
    public $selectedPeriod = 'month';
    public $staff = [];
    public $performanceData = [];
    public $showStaffSelector = false;
    public $showPeriodSelector = false;
    public $periodOptions = [];

    public function mount()
    {
        $this->loadStaff();
        $this->loadPeriodOptions();
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

    public function loadPeriodOptions()
    {
        $this->periodOptions = [
            ['value' => 'week', 'label' => 'This Week'],
            ['value' => 'month', 'label' => 'This Month'],
            ['value' => 'quarter', 'label' => 'This Quarter'],
            ['value' => 'year', 'label' => 'This Year'],
        ];
    }

    public function loadPerformanceData()
    {
        if (!$this->selectedStaff) {
            $this->performanceData = [];
            return;
        }

        $staff = Staff::find($this->selectedStaff);
        if (!$staff) {
            $this->performanceData = [];
            return;
        }

        $startDate = $this->getPeriodStartDate();
        $endDate = $this->getPeriodEndDate();

        // Get appointments for the period
        $appointments = Appointment::where('staff_id', $this->selectedStaff)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get();

        // Calculate performance metrics
        $totalAppointments = $appointments->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();
        $noShowAppointments = $appointments->where('status', 'no_show')->count();
        $totalRevenue = $appointments->where('status', 'completed')->sum('total_price');
        $averageAppointmentValue = $completedAppointments > 0 ? $totalRevenue / $completedAppointments : 0;

        // Calculate satisfaction score (mock data for now)
        $satisfactionScore = $this->calculateSatisfactionScore($appointments);

        $this->performanceData = [
            'staff_name' => $staff->user->name,
            'period' => $this->getPeriodLabel(),
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'no_show_appointments' => $noShowAppointments,
            'total_revenue' => $totalRevenue,
            'average_appointment_value' => $averageAppointmentValue,
            'satisfaction_score' => $satisfactionScore,
            'completion_rate' => $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0,
            'cancellation_rate' => $totalAppointments > 0 ? ($cancelledAppointments / $totalAppointments) * 100 : 0,
            'no_show_rate' => $totalAppointments > 0 ? ($noShowAppointments / $totalAppointments) * 100 : 0,
        ];
    }

    private function getPeriodStartDate()
    {
        switch ($this->selectedPeriod) {
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'quarter':
                return Carbon::now()->startOfQuarter();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    private function getPeriodEndDate()
    {
        switch ($this->selectedPeriod) {
            case 'week':
                return Carbon::now()->endOfWeek();
            case 'month':
                return Carbon::now()->endOfMonth();
            case 'quarter':
                return Carbon::now()->endOfQuarter();
            case 'year':
                return Carbon::now()->endOfYear();
            default:
                return Carbon::now()->endOfMonth();
        }
    }

    private function getPeriodLabel()
    {
        switch ($this->selectedPeriod) {
            case 'week':
                return 'This Week';
            case 'month':
                return 'This Month';
            case 'quarter':
                return 'This Quarter';
            case 'year':
                return 'This Year';
            default:
                return 'This Month';
        }
    }

    private function calculateSatisfactionScore($appointments)
    {
        // Mock calculation - in real app, this would come from client feedback
        $completedAppointments = $appointments->where('status', 'completed');
        if ($completedAppointments->count() === 0) {
            return 0;
        }

        // Simulate satisfaction scores based on completion rate
        $completionRate = $completedAppointments->count() / $appointments->count();
        return round($completionRate * 100);
    }

    public function updatedSelectedStaff()
    {
        $this->loadPerformanceData();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadPerformanceData();
    }

    public function toggleStaffSelector()
    {
        $this->showStaffSelector = !$this->showStaffSelector;
    }

    public function togglePeriodSelector()
    {
        $this->showPeriodSelector = !$this->showPeriodSelector;
    }

    public function render()
    {
        return view('livewire.admin.staff.mobile-staff-performance');
    }
}

