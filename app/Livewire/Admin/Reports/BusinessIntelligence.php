<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class BusinessIntelligence extends Component
{
    public $timeframe = 'quarterly';
    public $comparisonPeriod = 'previous';

    public function mount()
    {
        // Initialize with current quarter data
    }

    public function getKPIs()
    {
        $currentPeriod = $this->getCurrentPeriod();
        $comparisonPeriod = $this->getComparisonPeriod();

        return [
            'revenue_growth' => $this->calculateGrowthRate(
                $this->getRevenue($comparisonPeriod),
                $this->getRevenue($currentPeriod)
            ),
            'appointment_growth' => $this->calculateGrowthRate(
                $this->getAppointmentCount($comparisonPeriod),
                $this->getAppointmentCount($currentPeriod)
            ),
            'client_growth' => $this->calculateGrowthRate(
                $this->getClientCount($comparisonPeriod),
                $this->getClientCount($currentPeriod)
            ),
            'avg_appointment_value' => $this->getAverageAppointmentValue($currentPeriod),
            'client_retention_rate' => $this->getClientRetentionRate($currentPeriod),
            'staff_utilization' => $this->getStaffUtilization($currentPeriod),
            'peak_hours_efficiency' => $this->getPeakHoursEfficiency($currentPeriod),
            'service_popularity_index' => $this->getServicePopularityIndex($currentPeriod),
        ];
    }

    public function getRevenueTrends()
    {
        $months = [];
        $revenue = [];
        $appointments = [];
        $clients = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->endOfMonth();
            
            $revenue[] = Invoice::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            $appointments[] = Appointment::whereBetween('appointment_date', [$monthStart, $monthEnd])->count();
            $clients[] = Client::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'appointments' => $appointments,
            'clients' => $clients,
        ];
    }

    public function getBusinessInsights()
    {
        $currentPeriod = $this->getCurrentPeriod();
        
        return [
            'best_performing_service' => $this->getBestPerformingService($currentPeriod),
            'top_revenue_staff' => $this->getTopRevenueStaff($currentPeriod),
            'most_loyal_clients' => $this->getMostLoyalClients($currentPeriod),
            'peak_booking_day' => $this->getPeakBookingDay($currentPeriod),
            'revenue_forecast' => $this->getRevenueForecast(),
            'capacity_utilization' => $this->getCapacityUtilization($currentPeriod),
            'seasonal_trends' => $this->getSeasonalTrends(),
            'client_lifetime_value_trend' => $this->getClientLifetimeValueTrend(),
        ];
    }

    public function getOperationalMetrics()
    {
        $currentPeriod = $this->getCurrentPeriod();
        
        return [
            'appointment_completion_rate' => $this->getAppointmentCompletionRate($currentPeriod),
            'average_wait_time' => $this->getAverageWaitTime($currentPeriod),
            'staff_efficiency_score' => $this->getStaffEfficiencyScore($currentPeriod),
            'client_satisfaction_proxy' => $this->getClientSatisfactionProxy($currentPeriod),
            'revenue_per_square_foot' => $this->getRevenuePerSquareFoot($currentPeriod),
            'inventory_turnover' => $this->getInventoryTurnover($currentPeriod),
        ];
    }

    public function getMarketAnalysis()
    {
        return [
            'service_mix_analysis' => $this->getServiceMixAnalysis(),
            'pricing_optimization' => $this->getPricingOptimization(),
            'competitive_positioning' => $this->getCompetitivePositioning(),
            'market_penetration' => $this->getMarketPenetration(),
            'growth_opportunities' => $this->getGrowthOpportunities(),
        ];
    }

    private function getCurrentPeriod()
    {
        return match($this->timeframe) {
            'monthly' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'quarterly' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'yearly' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
        };
    }

    private function getComparisonPeriod()
    {
        return match($this->timeframe) {
            'monthly' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'quarterly' => [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()],
            'yearly' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()],
        };
    }

    private function calculateGrowthRate($oldValue, $newValue)
    {
        if ($oldValue == 0) return $newValue > 0 ? 100 : 0;
        return (($newValue - $oldValue) / $oldValue) * 100;
    }

    private function getRevenue($period)
    {
        return Invoice::whereBetween('created_at', $period)->sum('total_amount');
    }

    private function getAppointmentCount($period)
    {
        return Appointment::whereBetween('appointment_date', $period)->count();
    }

    private function getClientCount($period)
    {
        return Client::whereBetween('created_at', $period)->count();
    }

    private function getAverageAppointmentValue($period)
    {
        $revenue = $this->getRevenue($period);
        $appointments = Appointment::whereBetween('appointment_date', $period)->where('status', 'completed')->count();
        return $appointments > 0 ? $revenue / $appointments : 0;
    }

    private function getClientRetentionRate($period)
    {
        $previousPeriod = $this->getComparisonPeriod();
        
        $previousClients = Appointment::whereBetween('appointment_date', $previousPeriod)
            ->where('status', 'completed')
            ->distinct('client_id')
            ->pluck('client_id');
        
        $currentClients = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->distinct('client_id')
            ->pluck('client_id');
        
        $retainedClients = $previousClients->intersect($currentClients)->count();
        $totalPreviousClients = $previousClients->count();
        
        return $totalPreviousClients > 0 ? ($retainedClients / $totalPreviousClients) * 100 : 0;
    }

    private function getStaffUtilization($period)
    {
        $totalStaff = Staff::count();
        if ($totalStaff == 0) return 0;
        
        $activeStaff = Staff::whereHas('appointments', function ($q) use ($period) {
            $q->whereBetween('appointment_date', $period);
        })->count();
        
        return ($activeStaff / $totalStaff) * 100;
    }

    private function getPeakHoursEfficiency($period)
    {
        $hourlyAppointments = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->selectRaw('HOUR(appointment_date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
        
        $peakHours = $hourlyAppointments->take(3)->sum('count');
        $totalAppointments = $hourlyAppointments->sum('count');
        
        return $totalAppointments > 0 ? ($peakHours / $totalAppointments) * 100 : 0;
    }

    private function getServicePopularityIndex($period)
    {
        $serviceCounts = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->selectRaw('service_id, COUNT(*) as count')
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->get();
        
        $totalServices = Service::count();
        $activeServices = $serviceCounts->count();
        
        return $totalServices > 0 ? ($activeServices / $totalServices) * 100 : 0;
    }

    private function getBestPerformingService($period)
    {
        return Appointment::with('service')
            ->whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->selectRaw('service_id, COUNT(*) as count')
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->first();
    }

    private function getTopRevenueStaff($period)
    {
        return Staff::with('user')
            ->whereHas('appointments', function ($q) use ($period) {
                $q->whereBetween('appointment_date', $period)->where('status', 'completed');
            })
            ->get()
            ->map(function ($staff) use ($period) {
                $revenue = Invoice::whereHas('appointment', function ($q) use ($staff, $period) {
                    $q->where('staff_id', $staff->id)
                      ->whereBetween('appointment_date', $period)
                      ->where('status', 'completed');
                })->whereBetween('created_at', $period)->sum('total_amount');
                
                return [
                    'staff' => $staff,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->first();
    }

    private function getMostLoyalClients($period)
    {
        return Client::with('user')
            ->whereHas('appointments', function ($q) use ($period) {
                $q->whereBetween('appointment_date', $period)->where('status', 'completed');
            })
            ->get()
            ->map(function ($client) use ($period) {
                $appointmentCount = $client->appointments()
                    ->whereBetween('appointment_date', $period)
                    ->where('status', 'completed')
                    ->count();
                
                return [
                    'client' => $client,
                    'appointment_count' => $appointmentCount,
                ];
            })
            ->sortByDesc('appointment_count')
            ->take(3);
    }

    private function getPeakBookingDay($period)
    {
        $dayCounts = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->selectRaw('DAYOFWEEK(appointment_date) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->orderBy('count', 'desc')
            ->first();
        
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $dayCounts ? $dayNames[$dayCounts->day_of_week - 1] : 'N/A';
    }

    private function getRevenueForecast()
    {
        // Simple linear regression based on last 6 months
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $i;
            $revenues[] = Invoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
        }
        
        // Simple linear regression
        $n = count($months);
        $sumX = array_sum($months);
        $sumY = array_sum($revenues);
        $sumXY = array_sum(array_map(function($x, $y) { return $x * $y; }, $months, $revenues));
        $sumXX = array_sum(array_map(function($x) { return $x * $x; }, $months));
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        $nextMonthRevenue = $slope * 6 + $intercept;
        
        return [
            'next_month' => max(0, $nextMonthRevenue),
            'growth_rate' => $slope,
            'confidence' => $this->calculateForecastConfidence($revenues),
        ];
    }

    private function calculateForecastConfidence($revenues)
    {
        $variance = array_sum(array_map(function($x) use ($revenues) { 
            return pow($x - array_sum($revenues)/count($revenues), 2); 
        }, $revenues)) / count($revenues);
        
        $cv = sqrt($variance) / (array_sum($revenues) / count($revenues));
        return max(0, min(100, 100 - ($cv * 50)));
    }

    private function getCapacityUtilization($period)
    {
        $totalPossibleAppointments = Staff::count() * 8 * 30; // Assuming 8 hours/day, 30 days
        $actualAppointments = Appointment::whereBetween('appointment_date', $period)->count();
        
        return $totalPossibleAppointments > 0 ? ($actualAppointments / $totalPossibleAppointments) * 100 : 0;
    }

    private function getSeasonalTrends()
    {
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyData[] = [
                'month' => $month->format('M'),
                'revenue' => Invoice::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount'),
            ];
        }
        
        return $monthlyData;
    }

    private function getClientLifetimeValueTrend()
    {
        $monthlyCLV = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Invoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            $clients = Client::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $monthlyCLV[] = [
                'month' => $month->format('M Y'),
                'clv' => $clients > 0 ? $revenue / $clients : 0,
            ];
        }
        
        return $monthlyCLV;
    }

    private function getAppointmentCompletionRate($period)
    {
        $total = Appointment::whereBetween('appointment_date', $period)->count();
        $completed = Appointment::whereBetween('appointment_date', $period)->where('status', 'completed')->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getAverageWaitTime($period)
    {
        // This would typically come from actual wait time data
        // For now, we'll estimate based on appointment duration vs scheduled time
        $appointments = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->get();
        
        $totalWaitTime = 0;
        $count = 0;
        
        foreach ($appointments as $appointment) {
            if ($appointment->duration) {
                $totalWaitTime += $appointment->duration;
                $count++;
            }
        }
        
        return $count > 0 ? $totalWaitTime / $count : 0;
    }

    private function getStaffEfficiencyScore($period)
    {
        $staffPerformance = Staff::with('appointments')
            ->whereHas('appointments', function ($q) use ($period) {
                $q->whereBetween('appointment_date', $period);
            })
            ->get()
            ->map(function ($staff) use ($period) {
                $appointments = $staff->appointments()->whereBetween('appointment_date', $period)->get();
                $completed = $appointments->where('status', 'completed')->count();
                $total = $appointments->count();
                
                return $total > 0 ? ($completed / $total) * 100 : 0;
            });
        
        return $staffPerformance->count() > 0 ? $staffPerformance->avg() : 0;
    }

    private function getClientSatisfactionProxy($period)
    {
        // Proxy based on repeat visits and completion rate
        $repeatClients = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->selectRaw('client_id, COUNT(*) as count')
            ->groupBy('client_id')
            ->having('count', '>', 1)
            ->count();
        
        $totalClients = Appointment::whereBetween('appointment_date', $period)
            ->where('status', 'completed')
            ->distinct('client_id')
            ->count();
        
        return $totalClients > 0 ? ($repeatClients / $totalClients) * 100 : 0;
    }

    private function getRevenuePerSquareFoot($period)
    {
        // Assuming 1000 sq ft salon space
        $revenue = $this->getRevenue($period);
        return $revenue / 1000;
    }

    private function getInventoryTurnover($period)
    {
        // This would require inventory movement data
        // For now, return a placeholder
        return 4.2; // Average inventory turnover for salons
    }

    private function getServiceMixAnalysis()
    {
        return Service::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed');
        }])->get()->map(function ($service) {
            return [
                'service' => $service,
                'appointment_count' => $service->appointments_count,
                'revenue_potential' => $service->appointments_count * $service->price,
            ];
        })->sortByDesc('revenue_potential');
    }

    private function getPricingOptimization()
    {
        $services = Service::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed');
        }])->get();
        
        $avgPrice = $services->avg('price');
        
        return $services->map(function ($service) use ($avgPrice) {
            $priceIndex = $service->price / $avgPrice;
            return [
                'service' => $service,
                'price_index' => $priceIndex,
                'demand' => $service->appointments_count,
                'recommendation' => $priceIndex > 1.2 ? 'Consider lowering' : ($priceIndex < 0.8 ? 'Consider raising' : 'Optimal'),
            ];
        });
    }

    private function getCompetitivePositioning()
    {
        return [
            'market_share' => 15.5, // Placeholder - would come from market research
            'price_positioning' => 'Premium',
            'service_differentiation' => 'High',
            'client_retention' => $this->getClientRetentionRate($this->getCurrentPeriod()),
        ];
    }

    private function getMarketPenetration()
    {
        $totalClients = Client::count();
        $localPopulation = 50000; // Placeholder - would come from demographic data
        
        return [
            'penetration_rate' => ($totalClients / $localPopulation) * 100,
            'growth_potential' => 'High',
            'target_segments' => ['Young Professionals', 'Families', 'Seniors'],
        ];
    }

    private function getGrowthOpportunities()
    {
        return [
            'service_expansion' => ['Spa Services', 'Wellness Programs', 'Retail Products'],
            'time_expansion' => ['Evening Hours', 'Weekend Services'],
            'client_segments' => ['Corporate Clients', 'Group Bookings'],
            'technology_upgrades' => ['Online Booking', 'Mobile App', 'Loyalty Program'],
        ];
    }

    public function render()
    {
        $kpis = $this->getKPIs();
        $trends = $this->getRevenueTrends();
        $insights = $this->getBusinessInsights();
        $operationalMetrics = $this->getOperationalMetrics();
        $marketAnalysis = $this->getMarketAnalysis();

        return view('livewire.admin.reports.business-intelligence', [
            'kpis' => $kpis,
            'trends' => $trends,
            'insights' => $insights,
            'operationalMetrics' => $operationalMetrics,
            'marketAnalysis' => $marketAnalysis,
        ])->layout('layouts.admin');
    }
}
