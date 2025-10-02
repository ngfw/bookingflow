<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CashDrawer;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class DailySalesReporting extends Component
{
    use WithPagination;

    public $selectedDate;
    public $reportType = 'summary';
    public $groupBy = 'hour';
    public $paymentMethodFilter = 'all';
    public $staffFilter = 'all';
    public $showComparison = false;
    public $comparisonDate;

    public function mount()
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->comparisonDate = Carbon::yesterday()->format('Y-m-d');
    }

    public function updatedSelectedDate()
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

    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
    }

    public function updatedStaffFilter()
    {
        $this->resetPage();
    }

    public function getDailySummary()
    {
        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->startOfDay();
        $endOfDay = $date->endOfDay();

        $invoices = Invoice::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'paid');

        $payments = Payment::whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->where('status', 'completed');

        if ($this->staffFilter !== 'all') {
            $invoices->where('created_by', $this->staffFilter);
            $payments->whereHas('invoice', function($query) {
                $query->where('created_by', $this->staffFilter);
            });
        }

        $totalRevenue = $payments->sum('amount');
        $totalTransactions = $payments->count();
        $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $paymentMethodBreakdown = Payment::whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->where('status', 'completed')
            ->when($this->staffFilter !== 'all', function($query) {
                $query->whereHas('invoice', function($q) {
                    $q->where('created_by', $this->staffFilter);
                });
            })
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $topProducts = Product::withCount(['invoiceItems' => function($query) use ($startOfDay, $endOfDay) {
            $query->whereHas('invoice', function($q) use ($startOfDay, $endOfDay) {
                $q->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'paid');
            });
        }])
        ->withSum(['invoiceItems as total_revenue' => function($query) use ($startOfDay, $endOfDay) {
            $query->whereHas('invoice', function($q) use ($startOfDay, $endOfDay) {
                $q->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'paid');
            });
        }], 'total_price')
        ->orderByDesc('invoice_items_count')
        ->limit(10)
        ->get();

        $topClients = Client::withCount(['invoices' => function($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'paid');
        }])
        ->withSum(['invoices as total_spent' => function($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'paid');
        }], 'total_amount')
        ->orderByDesc('total_spent')
        ->limit(10)
        ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction_value' => $averageTransactionValue,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'top_products' => $topProducts,
            'top_clients' => $topClients,
        ];
    }

    public function getHourlyBreakdown()
    {
        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->startOfDay();
        $endOfDay = $date->endOfDay();

        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourStart = $startOfDay->copy()->addHours($hour);
            $hourEnd = $hourStart->copy()->addHour();

            $query = Payment::whereBetween('payment_date', [$hourStart, $hourEnd])
                ->where('status', 'completed');

            if ($this->staffFilter !== 'all') {
                $query->whereHas('invoice', function($q) {
                    $q->where('created_by', $this->staffFilter);
                });
            }

            if ($this->paymentMethodFilter !== 'all') {
                $query->where('payment_method', $this->paymentMethodFilter);
            }

            $hourlyData[] = [
                'hour' => $hour,
                'hour_label' => $hourStart->format('H:i'),
                'revenue' => $query->sum('amount'),
                'transactions' => $query->count(),
            ];
        }

        return $hourlyData;
    }

    public function getStaffBreakdown()
    {
        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->startOfDay();
        $endOfDay = $date->endOfDay();

        return Payment::whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->where('status', 'completed')
            ->when($this->paymentMethodFilter !== 'all', function($query) {
                $query->where('payment_method', $this->paymentMethodFilter);
            })
            ->with('invoice')
            ->get()
            ->groupBy('invoice.created_by')
            ->map(function($payments, $userId) {
                $user = \App\Models\User::find($userId);
                return [
                    'user_id' => $userId,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'revenue' => $payments->sum('amount'),
                    'transactions' => $payments->count(),
                    'average_transaction' => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }

    public function getDetailedTransactions()
    {
        $date = Carbon::parse($this->selectedDate);
        $startOfDay = $date->startOfDay();
        $endOfDay = $date->endOfDay();

        $query = Payment::with(['invoice.client', 'invoice.items.product'])
            ->whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->where('status', 'completed');

        if ($this->staffFilter !== 'all') {
            $query->whereHas('invoice', function($q) {
                $q->where('created_by', $this->staffFilter);
            });
        }

        if ($this->paymentMethodFilter !== 'all') {
            $query->where('payment_method', $this->paymentMethodFilter);
        }

        return $query->orderBy('payment_date', 'desc')->paginate(20);
    }

    public function getComparisonData()
    {
        if (!$this->showComparison) {
            return null;
        }

        $comparisonDate = Carbon::parse($this->comparisonDate);
        $startOfDay = $comparisonDate->startOfDay();
        $endOfDay = $comparisonDate->endOfDay();

        $payments = Payment::whereBetween('payment_date', [$startOfDay, $endOfDay])
            ->where('status', 'completed');

        if ($this->staffFilter !== 'all') {
            $payments->whereHas('invoice', function($query) {
                $query->where('created_by', $this->staffFilter);
            });
        }

        return [
            'total_revenue' => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'average_transaction_value' => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
        ];
    }

    public function getCashDrawerData()
    {
        $date = Carbon::parse($this->selectedDate);
        
        return CashDrawer::where('date', $date)
            ->when($this->staffFilter !== 'all', function($query) {
                $query->where('user_id', $this->staffFilter);
            })
            ->with('user')
            ->get();
    }

    public function getStaffList()
    {
        return \App\Models\User::whereIn('role', ['admin', 'staff'])
            ->orderBy('name')
            ->get();
    }

    public function exportToCSV()
    {
        $summary = $this->getDailySummary();
        $hourlyData = $this->getHourlyBreakdown();
        
        $filename = 'daily_sales_' . $this->selectedDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($summary, $hourlyData) {
            $file = fopen('php://output', 'w');
            
            // Summary data
            fputcsv($file, ['Daily Sales Summary', $this->selectedDate]);
            fputcsv($file, ['Total Revenue', '$' . number_format($summary['total_revenue'], 2)]);
            fputcsv($file, ['Total Transactions', $summary['total_transactions']]);
            fputcsv($file, ['Average Transaction Value', '$' . number_format($summary['average_transaction_value'], 2)]);
            fputcsv($file, []);
            
            // Payment method breakdown
            fputcsv($file, ['Payment Method Breakdown']);
            fputcsv($file, ['Method', 'Amount', 'Count']);
            foreach ($summary['payment_method_breakdown'] as $payment) {
                fputcsv($file, [$payment->payment_method, '$' . number_format($payment->total, 2), $payment->count]);
            }
            fputcsv($file, []);
            
            // Hourly breakdown
            fputcsv($file, ['Hourly Breakdown']);
            fputcsv($file, ['Hour', 'Revenue', 'Transactions']);
            foreach ($hourlyData as $hour) {
                fputcsv($file, [$hour['hour_label'], '$' . number_format($hour['revenue'], 2), $hour['transactions']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $summary = $this->getDailySummary();
        $hourlyData = $this->getHourlyBreakdown();
        $staffBreakdown = $this->getStaffBreakdown();
        $detailedTransactions = $this->getDetailedTransactions();
        $comparisonData = $this->getComparisonData();
        $cashDrawerData = $this->getCashDrawerData();
        $staffList = $this->getStaffList();

        return view('livewire.admin.pos.daily-sales-reporting', [
            'summary' => $summary,
            'hourlyData' => $hourlyData,
            'staffBreakdown' => $staffBreakdown,
            'detailedTransactions' => $detailedTransactions,
            'comparisonData' => $comparisonData,
            'cashDrawerData' => $cashDrawerData,
            'staffList' => $staffList,
        ])->layout('layouts.admin');
    }
}
