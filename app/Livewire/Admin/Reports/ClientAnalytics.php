<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class ClientAnalytics extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $clientSegment = 'all';
    public $sortBy = 'total_spent';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonths(6)->format('Y-m-d');
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

    public function updatedClientSegment()
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

    public function getClientSegments()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        // Calculate client spending in date range
        $clientSpending = Invoice::whereBetween('created_at', $dateRange)
            ->selectRaw('client_id, SUM(total_amount) as total_spent')
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id');

        $totalClients = $clientSpending->count();
        $totalRevenue = $clientSpending->sum('total_spent');
        
        if ($totalClients === 0) {
            return [
                'vip' => 0,
                'regular' => 0,
                'new' => 0,
                'at_risk' => 0,
            ];
        }

        $averageSpending = $totalRevenue / $totalClients;
        
        $segments = [
            'vip' => 0,      // Top 20% spenders
            'regular' => 0,  // Middle 60% spenders
            'new' => 0,      // New clients (first appointment in range)
            'at_risk' => 0,  // Clients with declining visits
        ];

        foreach ($clientSpending as $clientId => $spending) {
            $client = Client::find($clientId);
            if (!$client) continue;

            $totalSpent = $spending->total_spent;
            $appointmentCount = Appointment::where('client_id', $clientId)
                ->whereBetween('appointment_date', $dateRange)
                ->where('status', 'completed')
                ->count();

            // Check if new client
            $firstAppointment = Appointment::where('client_id', $clientId)
                ->where('status', 'completed')
                ->orderBy('appointment_date')
                ->first();

            if ($firstAppointment && $firstAppointment->appointment_date >= $this->dateFrom) {
                $segments['new']++;
            }
            // VIP clients (top 20% spenders)
            elseif ($totalSpent >= $averageSpending * 1.5) {
                $segments['vip']++;
            }
            // At-risk clients (low spending, few visits)
            elseif ($totalSpent < $averageSpending * 0.5 && $appointmentCount < 2) {
                $segments['at_risk']++;
            }
            // Regular clients
            else {
                $segments['regular']++;
            }
        }

        return $segments;
    }

    public function getClientLifetimeValue()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        return Client::with(['user', 'appointments', 'invoices'])
            ->whereHas('invoices', function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })
            ->get()
            ->map(function ($client) use ($dateRange) {
                $totalSpent = $client->invoices()
                    ->whereBetween('created_at', $dateRange)
                    ->sum('total_amount');
                
                $appointmentCount = $client->appointments()
                    ->whereBetween('appointment_date', $dateRange)
                    ->where('status', 'completed')
                    ->count();
                
                $firstAppointment = $client->appointments()
                    ->where('status', 'completed')
                    ->orderBy('appointment_date')
                    ->first();
                
                $lastAppointment = $client->appointments()
                    ->where('status', 'completed')
                    ->orderBy('appointment_date', 'desc')
                    ->first();
                
                $avgAppointmentValue = $appointmentCount > 0 ? $totalSpent / $appointmentCount : 0;
                
                return [
                    'client' => $client,
                    'total_spent' => $totalSpent,
                    'appointment_count' => $appointmentCount,
                    'avg_appointment_value' => $avgAppointmentValue,
                    'first_appointment' => $firstAppointment?->appointment_date,
                    'last_appointment' => $lastAppointment?->appointment_date,
                    'days_since_last' => $lastAppointment ? Carbon::parse($lastAppointment->appointment_date)->diffInDays(Carbon::now()) : null,
                ];
            })
            ->sortByDesc($this->sortBy)
            ->paginate(20);
    }

    public function getClientRetentionRate()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        // Get clients who had appointments in the previous period
        $previousPeriodStart = Carbon::parse($this->dateFrom)->subMonths(6);
        $previousPeriodEnd = Carbon::parse($this->dateFrom);
        
        $previousClients = Appointment::whereBetween('appointment_date', [$previousPeriodStart, $previousPeriodEnd])
            ->where('status', 'completed')
            ->distinct('client_id')
            ->pluck('client_id');
        
        // Get clients who had appointments in current period
        $currentClients = Appointment::whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->distinct('client_id')
            ->pluck('client_id');
        
        $retainedClients = $previousClients->intersect($currentClients)->count();
        $totalPreviousClients = $previousClients->count();
        
        return $totalPreviousClients > 0 ? ($retainedClients / $totalPreviousClients) * 100 : 0;
    }

    public function getClientAcquisitionRate()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        // New clients (first appointment in this period)
        $newClients = Appointment::whereBetween('appointment_date', $dateRange)
            ->where('status', 'completed')
            ->get()
            ->filter(function ($appointment) use ($dateRange) {
                $firstAppointment = Appointment::where('client_id', $appointment->client_id)
                    ->where('status', 'completed')
                    ->orderBy('appointment_date')
                    ->first();
                
                return $firstAppointment && $firstAppointment->appointment_date >= $this->dateFrom;
            })
            ->unique('client_id')
            ->count();
        
        // Total clients
        $totalClients = Client::count();
        
        return $totalClients > 0 ? ($newClients / $totalClients) * 100 : 0;
    }

    public function getClientBehaviorInsights()
    {
        $dateRange = [$this->dateFrom, $this->dateTo];
        
        return [
            'avg_appointments_per_client' => Appointment::whereBetween('appointment_date', $dateRange)
                ->where('status', 'completed')
                ->selectRaw('client_id, COUNT(*) as count')
                ->groupBy('client_id')
                ->avg('count'),
            
            'avg_spending_per_client' => Invoice::whereBetween('created_at', $dateRange)
                ->selectRaw('client_id, SUM(total_amount) as total')
                ->groupBy('client_id')
                ->avg('total'),
            
            'most_popular_services' => Appointment::with('service')
                ->whereBetween('appointment_date', $dateRange)
                ->where('status', 'completed')
                ->selectRaw('service_id, COUNT(*) as count')
                ->groupBy('service_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($appointment) {
                    return [
                        'service_name' => $appointment->service->name ?? 'Unknown',
                        'count' => $appointment->count,
                    ];
                }),
            
            'peak_booking_times' => Appointment::whereBetween('appointment_date', $dateRange)
                ->where('status', 'completed')
                ->selectRaw('HOUR(appointment_time) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($appointment) {
                    return [
                        'hour' => $appointment->hour,
                        'time_label' => Carbon::createFromTime($appointment->hour)->format('g A'),
                        'count' => $appointment->count,
                    ];
                }),
        ];
    }

    public function render()
    {
        $segments = $this->getClientSegments();
        $clientLifetimeValue = $this->getClientLifetimeValue();
        $retentionRate = $this->getClientRetentionRate();
        $acquisitionRate = $this->getClientAcquisitionRate();
        $behaviorInsights = $this->getClientBehaviorInsights();

        return view('livewire.admin.reports.client-analytics', [
            'segments' => $segments,
            'clientLifetimeValue' => $clientLifetimeValue,
            'retentionRate' => $retentionRate,
            'acquisitionRate' => $acquisitionRate,
            'behaviorInsights' => $behaviorInsights,
        ])->layout('layouts.admin');
    }
}
