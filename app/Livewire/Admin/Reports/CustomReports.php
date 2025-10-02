<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class CustomReports extends Component
{
    use WithPagination;

    public $reportType = 'appointments';
    public $dateFrom = '';
    public $dateTo = '';
    public $filters = [];
    public $groupBy = 'daily';
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $includeCharts = true;
    public $reportTitle = '';
    public $reportDescription = '';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->reportTitle = 'Custom Report - ' . Carbon::now()->format('M d, Y');
    }

    public function updatedReportType()
    {
        $this->resetFilters();
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedGroupBy()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSortDirection()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filters = [];
        $this->resetPage();
    }

    public function addFilter($type, $field, $operator = '=', $value = '')
    {
        $this->filters[] = [
            'type' => $type,
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
        ];
        $this->resetPage();
    }

    public function removeFilter($index)
    {
        unset($this->filters[$index]);
        $this->filters = array_values($this->filters);
        $this->resetPage();
    }

    public function generateReport()
    {
        $this->resetPage();
    }

    public function getReportData()
    {
        $query = $this->buildQuery();
        return $query->paginate(50);
    }

    public function getReportSummary()
    {
        $query = $this->buildQuery();
        
        switch ($this->reportType) {
            case 'appointments':
                return [
                    'total_count' => $query->count(),
                    'total_revenue' => $query->sum('total_amount'),
                    'avg_duration' => $query->avg('duration'),
                    'completion_rate' => $query->where('status', 'completed')->count() / max($query->count(), 1) * 100,
                ];
            case 'clients':
                return [
                    'total_count' => $query->count(),
                    'new_clients' => $query->where('created_at', '>=', $this->dateFrom)->count(),
                    'avg_appointments' => $query->withCount('appointments')->avg('appointments_count'),
                    'total_spent' => $query->withSum('invoices', 'total_amount')->sum('invoices_sum_total_amount'),
                ];
            case 'services':
                return [
                    'total_count' => $query->count(),
                    'total_bookings' => $query->withCount('appointments')->sum('appointments_count'),
                    'avg_price' => $query->avg('price'),
                    'total_revenue' => $query->withCount('appointments')->get()->sum(function ($service) {
                        return $service->appointments_count * $service->price;
                    }),
                ];
            case 'staff':
                return [
                    'total_count' => $query->count(),
                    'active_staff' => $query->whereHas('appointments')->count(),
                    'avg_appointments' => $query->withCount('appointments')->avg('appointments_count'),
                    'total_revenue' => $query->withCount('appointments')->get()->sum(function ($staff) {
                        return $staff->appointments_count * 50; // Average service price
                    }),
                ];
            case 'financial':
                return [
                    'total_revenue' => $query->sum('total_amount'),
                    'total_payments' => $query->where('status', 'paid')->sum('total_amount'),
                    'pending_amount' => $query->where('status', 'pending')->sum('total_amount'),
                    'avg_invoice_value' => $query->avg('total_amount'),
                ];
            default:
                return [];
        }
    }

    public function getChartData()
    {
        if (!$this->includeCharts) return null;

        $query = $this->buildQuery();
        
        switch ($this->groupBy) {
            case 'daily':
                return $this->getDailyChartData($query);
            case 'weekly':
                return $this->getWeeklyChartData($query);
            case 'monthly':
                return $this->getMonthlyChartData($query);
            case 'service':
                return $this->getServiceChartData($query);
            case 'staff':
                return $this->getStaffChartData($query);
            default:
                return null;
        }
    }

    private function buildQuery()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        switch ($this->reportType) {
            case 'appointments':
                $query = Appointment::with(['client.user', 'service', 'staff.user'])
                    ->whereBetween('appointment_date', $dateRange);
                break;
            case 'clients':
                $query = Client::with(['user', 'appointments', 'invoices'])
                    ->whereHas('appointments', function ($q) use ($dateRange) {
                        $q->whereBetween('appointment_date', $dateRange);
                    });
                break;
            case 'services':
                $query = Service::withCount(['appointments' => function ($q) use ($dateRange) {
                    $q->whereBetween('appointment_date', $dateRange);
                }]);
                break;
            case 'staff':
                $query = Staff::with(['user'])
                    ->withCount(['appointments' => function ($q) use ($dateRange) {
                        $q->whereBetween('appointment_date', $dateRange);
                    }]);
                break;
            case 'financial':
                $query = Invoice::with(['appointment.client.user', 'appointment.service'])
                    ->whereBetween('created_at', $dateRange);
                break;
            default:
                $query = Appointment::whereBetween('appointment_date', $dateRange);
        }

        // Apply filters
        foreach ($this->filters as $filter) {
            $query = $this->applyFilter($query, $filter);
        }

        // Apply sorting
        if ($this->sortBy && $this->sortDirection) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }

    private function applyFilter($query, $filter)
    {
        $field = $filter['field'];
        $operator = $filter['operator'];
        $value = $filter['value'];

        if (empty($value)) return $query;

        switch ($operator) {
            case '=':
                return $query->where($field, $value);
            case '!=':
                return $query->where($field, '!=', $value);
            case '>':
                return $query->where($field, '>', $value);
            case '>=':
                return $query->where($field, '>=', $value);
            case '<':
                return $query->where($field, '<', $value);
            case '<=':
                return $query->where($field, '<=', $value);
            case 'like':
                return $query->where($field, 'like', "%{$value}%");
            case 'in':
                return $query->whereIn($field, explode(',', $value));
            case 'not_in':
                return $query->whereNotIn($field, explode(',', $value));
            default:
                return $query;
        }
    }

    private function getDailyChartData($query)
    {
        $data = [];
        $startDate = Carbon::parse($this->dateFrom);
        $endDate = Carbon::parse($this->dateTo);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $count = $query->whereDate('appointment_date', $date)->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        return $data;
    }

    private function getWeeklyChartData($query)
    {
        $data = [];
        $startDate = Carbon::parse($this->dateFrom)->startOfWeek();
        $endDate = Carbon::parse($this->dateTo)->endOfWeek();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addWeek()) {
            $count = $query->whereBetween('appointment_date', [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek()
            ])->count();
            $data[] = [
                'week' => $date->format('M d'),
                'count' => $count,
            ];
        }

        return $data;
    }

    private function getMonthlyChartData($query)
    {
        $data = [];
        $startDate = Carbon::parse($this->dateFrom)->startOfMonth();
        $endDate = Carbon::parse($this->dateTo)->endOfMonth();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonth()) {
            $count = $query->whereBetween('appointment_date', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])->count();
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        return $data;
    }

    private function getServiceChartData($query)
    {
        return $query->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'service' => $service->name,
                    'count' => $service->appointments_count,
                ];
            });
    }

    private function getStaffChartData($query)
    {
        return $query->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($staff) {
                return [
                    'staff' => $staff->user->name,
                    'count' => $staff->appointments_count,
                ];
            });
    }

    public function getAvailableFields()
    {
        switch ($this->reportType) {
            case 'appointments':
                return [
                    'appointment_date' => 'Appointment Date',
                    'appointment_time' => 'Appointment Time',
                    'status' => 'Status',
                    'duration' => 'Duration',
                    'service_id' => 'Service',
                    'staff_id' => 'Staff',
                    'client_id' => 'Client',
                ];
            case 'clients':
                return [
                    'created_at' => 'Registration Date',
                    'phone' => 'Phone',
                    'address' => 'Address',
                    'city' => 'City',
                    'state' => 'State',
                    'zip_code' => 'ZIP Code',
                ];
            case 'services':
                return [
                    'name' => 'Service Name',
                    'price' => 'Price',
                    'duration' => 'Duration',
                    'category' => 'Category',
                    'is_active' => 'Active Status',
                ];
            case 'staff':
                return [
                    'created_at' => 'Hire Date',
                    'specialization' => 'Specialization',
                    'experience_years' => 'Experience Years',
                    'is_active' => 'Active Status',
                ];
            case 'financial':
                return [
                    'created_at' => 'Invoice Date',
                    'status' => 'Payment Status',
                    'total_amount' => 'Total Amount',
                    'payment_method' => 'Payment Method',
                ];
            default:
                return [];
        }
    }

    public function getAvailableOperators()
    {
        return [
            '=' => 'Equals',
            '!=' => 'Not Equals',
            '>' => 'Greater Than',
            '>=' => 'Greater Than or Equal',
            '<' => 'Less Than',
            '<=' => 'Less Than or Equal',
            'like' => 'Contains',
            'in' => 'In List',
            'not_in' => 'Not In List',
        ];
    }

    public function exportToCSV()
    {
        $data = $this->getReportData();
        $filename = 'custom_report_' . $this->reportType . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        // This would typically generate and download a CSV file
        // For now, we'll return the data structure
        return [
            'filename' => $filename,
            'data' => $data,
        ];
    }

    public function exportToPDF()
    {
        $data = $this->getReportData();
        $summary = $this->getReportSummary();
        $filename = 'custom_report_' . $this->reportType . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        // This would typically generate and download a PDF file
        // For now, we'll return the data structure
        return [
            'filename' => $filename,
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function render()
    {
        $reportData = $this->getReportData();
        $reportSummary = $this->getReportSummary();
        $chartData = $this->getChartData();
        $availableFields = $this->getAvailableFields();
        $availableOperators = $this->getAvailableOperators();

        return view('livewire.admin.reports.custom-reports', [
            'reportData' => $reportData,
            'reportSummary' => $reportSummary,
            'chartData' => $chartData,
            'availableFields' => $availableFields,
            'availableOperators' => $availableOperators,
        ])->layout('layouts.admin');
    }
}
