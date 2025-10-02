<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Staff;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;

class StaffPerformance extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $staffFilter = '';
    public $sortBy = 'appointment_count';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedStaffFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getStaffPerformanceData()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        $query = Staff::with(['user', 'services', 'appointments' => function ($q) use ($dateRange) {
            $q->whereBetween('appointment_date', $dateRange);
        }]);

        if ($this->staffFilter) {
            $query->where('id', $this->staffFilter);
        }

        return $query->get()->map(function ($staff) use ($dateRange) {
            $appointments = $staff->appointments()
                ->whereBetween('appointment_date', $dateRange)
                ->get();

            $completedAppointments = $appointments->where('status', 'completed');
            $cancelledAppointments = $appointments->where('status', 'cancelled');
            $noShowAppointments = $appointments->where('status', 'no_show');

            // Calculate revenue from completed appointments
            $revenue = Invoice::whereHas('appointment', function ($q) use ($staff, $dateRange) {
                $q->where('staff_id', $staff->id)
                  ->whereBetween('appointment_date', $dateRange)
                  ->where('status', 'completed');
            })->whereBetween('created_at', $dateRange)->sum('total_amount');

            // Calculate average appointment duration
            $avgDuration = $completedAppointments->avg('duration') ?? 0;

            // Calculate completion rate
            $totalAppointments = $appointments->count();
            $completionRate = $totalAppointments > 0 ? ($completedAppointments->count() / $totalAppointments) * 100 : 0;

            // Calculate cancellation rate
            $cancellationRate = $totalAppointments > 0 ? ($cancelledAppointments->count() / $totalAppointments) * 100 : 0;

            // Calculate no-show rate
            $noShowRate = $totalAppointments > 0 ? ($noShowAppointments->count() / $totalAppointments) * 100 : 0;

            // Get service breakdown
            $serviceBreakdown = $completedAppointments->groupBy('service_id')->map(function ($serviceAppointments) {
                return $serviceAppointments->count();
            });

            // Calculate hourly performance
            $hourlyPerformance = $completedAppointments->groupBy(function ($appointment) {
                return Carbon::parse($appointment->appointment_time)->format('H');
            })->map(function ($hourAppointments) {
                return $hourAppointments->count();
            });

            return [
                'staff' => $staff,
                'appointment_count' => $totalAppointments,
                'completed_count' => $completedAppointments->count(),
                'cancelled_count' => $cancelledAppointments->count(),
                'no_show_count' => $noShowAppointments->count(),
                'revenue' => $revenue,
                'avg_duration' => $avgDuration,
                'completion_rate' => $completionRate,
                'cancellation_rate' => $cancellationRate,
                'no_show_rate' => $noShowRate,
                'service_breakdown' => $serviceBreakdown,
                'hourly_performance' => $hourlyPerformance,
            ];
        })->sortByDesc($this->sortBy)->paginate(10);
    }

    public function getOverallStats()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        $totalAppointments = Appointment::whereBetween('appointment_date', $dateRange)->count();
        $completedAppointments = Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'completed')->count();
        $totalRevenue = Invoice::whereBetween('created_at', $dateRange)->sum('total_amount');
        $activeStaff = Staff::whereHas('appointments', function ($q) use ($dateRange) {
            $q->whereBetween('appointment_date', $dateRange);
        })->count();

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'completion_rate' => $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0,
            'total_revenue' => $totalRevenue,
            'active_staff' => $activeStaff,
            'avg_revenue_per_staff' => $activeStaff > 0 ? $totalRevenue / $activeStaff : 0,
        ];
    }

    public function getTopPerformers()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        return Staff::with('user')
            ->whereHas('appointments', function ($q) use ($dateRange) {
                $q->whereBetween('appointment_date', $dateRange)
                  ->where('status', 'completed');
            })
            ->get()
            ->map(function ($staff) use ($dateRange) {
                $completedAppointments = $staff->appointments()
                    ->whereBetween('appointment_date', $dateRange)
                    ->where('status', 'completed')
                    ->count();

                $revenue = Invoice::whereHas('appointment', function ($q) use ($staff, $dateRange) {
                    $q->where('staff_id', $staff->id)
                      ->whereBetween('appointment_date', $dateRange)
                      ->where('status', 'completed');
                })->whereBetween('created_at', $dateRange)->sum('total_amount');

                return [
                    'staff' => $staff,
                    'appointments' => $completedAppointments,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);
    }

    public function getServicePerformance()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        return Appointment::with(['service', 'staff.user'])
            ->whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('service_id, staff_id, COUNT(*) as count, AVG(duration) as avg_duration')
            ->groupBy('service_id', 'staff_id')
            ->orderBy('count', 'desc')
            ->get()
            ->groupBy('service_id')
            ->map(function ($serviceAppointments) {
                $service = $serviceAppointments->first()->service;
                $totalCount = $serviceAppointments->sum('count');
                $avgDuration = $serviceAppointments->avg('avg_duration');
                
                return [
                    'service' => $service,
                    'total_appointments' => $totalCount,
                    'avg_duration' => $avgDuration,
                    'staff_breakdown' => $serviceAppointments->map(function ($appointment) {
                        return [
                            'staff_name' => $appointment->staff->user->name ?? 'Unknown',
                            'count' => $appointment->count,
                            'avg_duration' => $appointment->avg_duration,
                        ];
                    }),
                ];
            })
            ->take(5);
    }

    public function render()
    {
        $performanceData = $this->getStaffPerformanceData();
        $overallStats = $this->getOverallStats();
        $topPerformers = $this->getTopPerformers();
        $servicePerformance = $this->getServicePerformance();
        $allStaff = Staff::with('user')->get();

        return view('livewire.admin.reports.staff-performance', [
            'performanceData' => $performanceData,
            'overallStats' => $overallStats,
            'topPerformers' => $topPerformers,
            'servicePerformance' => $servicePerformance,
            'allStaff' => $allStaff,
        ])->layout('layouts.admin');
    }
}
