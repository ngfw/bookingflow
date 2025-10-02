<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Client;
use Carbon\Carbon;

class Appointments extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $serviceFilter = '';
    public $staffFilter = '';
    public $groupBy = 'daily';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedServiceFilter()
    {
        $this->resetPage();
    }

    public function updatedStaffFilter()
    {
        $this->resetPage();
    }

    public function updatedGroupBy()
    {
        $this->resetPage();
    }

    public function getAppointmentStats()
    {
        $query = Appointment::whereBetween('appointment_date', [$this->dateFrom, $this->dateTo]);
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->serviceFilter) {
            $query->where('service_id', $this->serviceFilter);
        }
        
        if ($this->staffFilter) {
            $query->where('staff_id', $this->staffFilter);
        }

        return [
            'total_appointments' => $query->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'confirmed' => $query->where('status', 'confirmed')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'no_show' => $query->where('status', 'no_show')->count(),
        ];
    }

    public function getAppointmentTrend()
    {
        $query = Appointment::whereBetween('appointment_date', [$this->dateFrom, $this->dateTo]);
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->serviceFilter) {
            $query->where('service_id', $this->serviceFilter);
        }
        
        if ($this->staffFilter) {
            $query->where('staff_id', $this->staffFilter);
        }

        if ($this->groupBy === 'daily') {
            return $query->selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->paginate(15);
        } elseif ($this->groupBy === 'weekly') {
            return $query->selectRaw('YEAR(appointment_date) as year, WEEK(appointment_date) as week, COUNT(*) as count')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->paginate(15);
        } else {
            return $query->selectRaw('YEAR(appointment_date) as year, MONTH(appointment_date) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->paginate(15);
        }
    }

    public function getServiceBreakdown()
    {
        $query = Appointment::with('service')
            ->whereBetween('appointment_date', [$this->dateFrom, $this->dateTo]);
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->staffFilter) {
            $query->where('staff_id', $this->staffFilter);
        }

        return $query->selectRaw('service_id, COUNT(*) as appointment_count')
            ->groupBy('service_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($appointment) {
                return [
                    'service_name' => $appointment->service->name ?? 'Unknown Service',
                    'appointment_count' => $appointment->appointment_count,
                ];
            });
    }

    public function getStaffBreakdown()
    {
        $query = Appointment::with('staff.user')
            ->whereBetween('appointment_date', [$this->dateFrom, $this->dateTo]);
        
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        if ($this->serviceFilter) {
            $query->where('service_id', $this->serviceFilter);
        }

        return $query->selectRaw('staff_id, COUNT(*) as appointment_count')
            ->groupBy('staff_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($appointment) {
                return [
                    'staff_name' => $appointment->staff->user->name ?? 'Unknown Staff',
                    'appointment_count' => $appointment->appointment_count,
                ];
            });
    }

    public function getHourlyDistribution()
    {
        $query = Appointment::whereBetween('appointment_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed');
        
        if ($this->serviceFilter) {
            $query->where('service_id', $this->serviceFilter);
        }
        
        if ($this->staffFilter) {
            $query->where('staff_id', $this->staffFilter);
        }

        return $query->selectRaw('HOUR(appointment_date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($appointment) {
                return [
                    'hour' => $appointment->hour,
                    'count' => $appointment->count,
                    'time_label' => Carbon::createFromTime($appointment->hour)->format('g A'),
                ];
            });
    }

    public function getCancellationReasons()
    {
        return Appointment::whereBetween('appointment_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'cancelled')
            ->whereNotNull('cancellation_reason')
            ->selectRaw('cancellation_reason, COUNT(*) as count')
            ->groupBy('cancellation_reason')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function render()
    {
        $stats = $this->getAppointmentStats();
        $trendData = $this->getAppointmentTrend();
        $serviceBreakdown = $this->getServiceBreakdown();
        $staffBreakdown = $this->getStaffBreakdown();
        $hourlyDistribution = $this->getHourlyDistribution();
        $cancellationReasons = $this->getCancellationReasons();

        $services = Service::orderBy('name')->get();
        $staff = Staff::with('user')->get();

        return view('livewire.admin.reports.appointments', [
            'stats' => $stats,
            'trendData' => $trendData,
            'serviceBreakdown' => $serviceBreakdown,
            'staffBreakdown' => $staffBreakdown,
            'hourlyDistribution' => $hourlyDistribution,
            'cancellationReasons' => $cancellationReasons,
            'services' => $services,
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
