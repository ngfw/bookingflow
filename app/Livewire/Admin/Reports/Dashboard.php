<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $dateRange = 'this_month';
    public $selectedPeriod = 'monthly';

    public function mount()
    {
        // Initialize with current month data
    }

    public function updatedDateRange()
    {
        // Refresh data when date range changes
    }

    public function getDateRange()
    {
        return match($this->dateRange) {
            'today' => [Carbon::today(), Carbon::today()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'last_quarter' => [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'last_year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    public function getOverviewStats()
    {
        $dateRange = $this->getDateRange();
        
        return [
            'total_revenue' => Invoice::whereBetween('created_at', $dateRange)->sum('total_amount'),
            'total_appointments' => Appointment::whereBetween('appointment_date', $dateRange)->count(),
            'completed_appointments' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'cancelled')->count(),
            'new_clients' => Client::whereBetween('created_at', $dateRange)->count(),
            'total_payments' => Payment::whereBetween('payment_date', $dateRange)->where('status', 'completed')->sum('amount'),
            'pending_invoices' => Invoice::whereBetween('created_at', $dateRange)->where('status', 'sent')->sum('balance_due'),
            'average_appointment_value' => $this->getAverageAppointmentValue($dateRange),
        ];
    }

    public function getRevenueTrend()
    {
        $months = [];
        $revenue = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $revenue[] = Invoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
        }
        
        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }

    public function getTopServices()
    {
        $dateRange = $this->getDateRange();
        
        return Appointment::with('service')
            ->whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('service_id, COUNT(*) as appointment_count')
            ->groupBy('service_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'service_name' => $appointment->service->name ?? 'Unknown Service',
                    'appointment_count' => $appointment->appointment_count,
                    'revenue' => $appointment->service->price ?? 0,
                ];
            });
    }

    public function getTopStaff()
    {
        $dateRange = $this->getDateRange();
        
        return Appointment::with('staff.user')
            ->whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('staff_id, COUNT(*) as appointment_count')
            ->groupBy('staff_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'staff_name' => $appointment->staff->user->name ?? 'Unknown Staff',
                    'appointment_count' => $appointment->appointment_count,
                ];
            });
    }

    public function getTopClients()
    {
        $dateRange = $this->getDateRange();
        
        return Appointment::with('client.user')
            ->whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('client_id, COUNT(*) as appointment_count')
            ->groupBy('client_id')
            ->orderBy('appointment_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'client_name' => $appointment->client->user->name ?? 'Unknown Client',
                    'appointment_count' => $appointment->appointment_count,
                ];
            });
    }

    public function getAppointmentStatusDistribution()
    {
        $dateRange = $this->getDateRange();
        
        return [
            'completed' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'completed')->count(),
            'pending' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'pending')->count(),
            'confirmed' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'confirmed')->count(),
            'cancelled' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'cancelled')->count(),
            'no_show' => Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'no_show')->count(),
        ];
    }

    public function getPaymentMethodDistribution()
    {
        $dateRange = $this->getDateRange();
        
        return Payment::whereBetween('payment_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($payment) {
                return [
                    'method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
                    'count' => $payment->count,
                    'total_amount' => $payment->total_amount,
                ];
            });
    }

    private function getAverageAppointmentValue($dateRange)
    {
        $totalRevenue = Invoice::whereBetween('created_at', $dateRange)->sum('total_amount');
        $totalAppointments = Appointment::whereBetween('appointment_date', $dateRange)->where('status', 'completed')->count();
        
        return $totalAppointments > 0 ? $totalRevenue / $totalAppointments : 0;
    }

    public function render()
    {
        $overviewStats = $this->getOverviewStats();
        $revenueTrend = $this->getRevenueTrend();
        $topServices = $this->getTopServices();
        $topStaff = $this->getTopStaff();
        $topClients = $this->getTopClients();
        $appointmentStatusDistribution = $this->getAppointmentStatusDistribution();
        $paymentMethodDistribution = $this->getPaymentMethodDistribution();

        return view('livewire.admin.reports.dashboard', [
            'overviewStats' => $overviewStats,
            'revenueTrend' => $revenueTrend,
            'topServices' => $topServices,
            'topStaff' => $topStaff,
            'topClients' => $topClients,
            'appointmentStatusDistribution' => $appointmentStatusDistribution,
            'paymentMethodDistribution' => $paymentMethodDistribution,
        ])->layout('layouts.admin');
    }
}
