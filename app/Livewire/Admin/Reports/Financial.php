<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Appointment;
use Carbon\Carbon;

class Financial extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $reportType = 'revenue';
    public $groupBy = 'monthly';

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

    public function updatedReportType()
    {
        $this->resetPage();
    }

    public function updatedGroupBy()
    {
        $this->resetPage();
    }

    public function getRevenueReport()
    {
        $query = Invoice::whereBetween('created_at', [$this->dateFrom, $this->dateTo]);
        
        if ($this->groupBy === 'daily') {
            return $query->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as invoice_count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->paginate(15);
        } elseif ($this->groupBy === 'weekly') {
            return $query->selectRaw('YEAR(created_at) as year, WEEK(created_at) as week, SUM(total_amount) as revenue, COUNT(*) as invoice_count')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->paginate(15);
        } else {
            return $query->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as invoice_count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->paginate(15);
        }
    }

    public function getPaymentReport()
    {
        $query = Payment::whereBetween('payment_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed');
        
        if ($this->groupBy === 'daily') {
            return $query->selectRaw('DATE(payment_date) as date, SUM(amount) as total_amount, COUNT(*) as payment_count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->paginate(15);
        } elseif ($this->groupBy === 'weekly') {
            return $query->selectRaw('YEAR(payment_date) as year, WEEK(payment_date) as week, SUM(amount) as total_amount, COUNT(*) as payment_count')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->paginate(15);
        } else {
            return $query->selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total_amount, COUNT(*) as payment_count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->paginate(15);
        }
    }

    public function getAppointmentRevenueReport()
    {
        $query = Appointment::with(['service', 'client.user'])
            ->whereBetween('appointment_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed');
        
        if ($this->groupBy === 'daily') {
            return $query->selectRaw('DATE(appointment_date) as date, COUNT(*) as appointment_count, SUM(services.price) as revenue')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->paginate(15);
        } elseif ($this->groupBy === 'weekly') {
            return $query->selectRaw('YEAR(appointment_date) as year, WEEK(appointment_date) as week, COUNT(*) as appointment_count, SUM(services.price) as revenue')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->paginate(15);
        } else {
            return $query->selectRaw('YEAR(appointment_date) as year, MONTH(appointment_date) as month, COUNT(*) as appointment_count, SUM(services.price) as revenue')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->paginate(15);
        }
    }

    public function getFinancialSummary()
    {
        return [
            'total_revenue' => Invoice::whereBetween('created_at', [$this->dateFrom, $this->dateTo])->sum('total_amount'),
            'total_payments' => Payment::whereBetween('payment_date', [$this->dateFrom, $this->dateTo])->where('status', 'completed')->sum('amount'),
            'pending_amount' => Invoice::whereBetween('created_at', [$this->dateFrom, $this->dateTo])->where('status', 'sent')->sum('balance_due'),
            'refunded_amount' => Payment::whereBetween('payment_date', [$this->dateFrom, $this->dateTo])->where('status', 'refunded')->sum('amount'),
            'invoice_count' => Invoice::whereBetween('created_at', [$this->dateFrom, $this->dateTo])->count(),
            'payment_count' => Payment::whereBetween('payment_date', [$this->dateFrom, $this->dateTo])->where('status', 'completed')->count(),
        ];
    }

    public function getPaymentMethodBreakdown()
    {
        return Payment::whereBetween('payment_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($payment) {
                return [
                    'method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
                    'count' => $payment->count,
                    'total_amount' => $payment->total_amount,
                    'percentage' => 0, // Will be calculated in view
                ];
            });
    }

    public function render()
    {
        $summary = $this->getFinancialSummary();
        $paymentMethods = $this->getPaymentMethodBreakdown();
        
        // Calculate percentages for payment methods
        $totalAmount = $paymentMethods->sum('total_amount');
        $paymentMethods = $paymentMethods->map(function ($method) use ($totalAmount) {
            $method['percentage'] = $totalAmount > 0 ? ($method['total_amount'] / $totalAmount) * 100 : 0;
            return $method;
        });

        $reportData = match($this->reportType) {
            'revenue' => $this->getRevenueReport(),
            'payments' => $this->getPaymentReport(),
            'appointments' => $this->getAppointmentRevenueReport(),
            default => $this->getRevenueReport(),
        };

        return view('livewire.admin.reports.financial', [
            'summary' => $summary,
            'paymentMethods' => $paymentMethods,
            'reportData' => $reportData,
        ])->layout('layouts.admin');
    }
}
