<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use Carbon\Carbon;

class PredictiveAnalytics extends Component
{
    public $forecastPeriod = '3_months';
    public $confidenceLevel = 85;
    public $includeSeasonality = true;
    public $includeTrends = true;

    public function mount()
    {
        // Initialize predictive analytics
    }

    public function getRevenueForecast()
    {
        $historicalData = $this->getHistoricalRevenueData();
        $forecastData = $this->calculateRevenueForecast($historicalData);
        
        return [
            'historical' => $historicalData,
            'forecast' => $forecastData,
            'accuracy' => $this->calculateForecastAccuracy($historicalData),
            'trend' => $this->calculateTrend($historicalData),
            'seasonality' => $this->calculateSeasonality($historicalData),
        ];
    }

    public function getAppointmentForecast()
    {
        $historicalData = $this->getHistoricalAppointmentData();
        $forecastData = $this->calculateAppointmentForecast($historicalData);
        
        return [
            'historical' => $historicalData,
            'forecast' => $forecastData,
            'peak_periods' => $this->identifyPeakPeriods($historicalData),
            'demand_patterns' => $this->analyzeDemandPatterns($historicalData),
        ];
    }

    public function getClientBehaviorPredictions()
    {
        return [
            'churn_prediction' => $this->predictClientChurn(),
            'lifetime_value_prediction' => $this->predictClientLifetimeValue(),
            'next_appointment_prediction' => $this->predictNextAppointment(),
            'service_recommendations' => $this->generateServiceRecommendations(),
        ];
    }

    public function getStaffPerformancePredictions()
    {
        return [
            'performance_trends' => $this->predictStaffPerformanceTrends(),
            'capacity_optimization' => $this->optimizeStaffCapacity(),
            'skill_gaps' => $this->identifySkillGaps(),
            'training_recommendations' => $this->generateTrainingRecommendations(),
        ];
    }

    public function getInventoryPredictions()
    {
        return [
            'demand_forecast' => $this->forecastInventoryDemand(),
            'reorder_points' => $this->calculateReorderPoints(),
            'waste_prediction' => $this->predictInventoryWaste(),
            'cost_optimization' => $this->optimizeInventoryCosts(),
        ];
    }

    public function getBusinessInsights()
    {
        return [
            'growth_opportunities' => $this->identifyGrowthOpportunities(),
            'risk_factors' => $this->identifyRiskFactors(),
            'market_trends' => $this->analyzeMarketTrends(),
            'competitive_analysis' => $this->performCompetitiveAnalysis(),
        ];
    }

    private function getHistoricalRevenueData()
    {
        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Invoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
                'date' => $month->format('Y-m'),
            ];
        }
        return $data;
    }

    private function getHistoricalAppointmentData()
    {
        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $appointments = Appointment::whereYear('appointment_date', $month->year)
                ->whereMonth('appointment_date', $month->month)
                ->where('status', 'completed')
                ->count();
            
            $data[] = [
                'month' => $month->format('M Y'),
                'appointments' => $appointments,
                'date' => $month->format('Y-m'),
            ];
        }
        return $data;
    }

    private function calculateRevenueForecast($historicalData)
    {
        $revenues = collect($historicalData)->pluck('revenue')->toArray();
        $forecast = [];
        
        // Simple linear regression for trend
        $trend = $this->calculateLinearTrend($revenues);
        
        // Calculate seasonal factors
        $seasonalFactors = $this->calculateSeasonalFactors($revenues);
        
        $forecastMonths = $this->getForecastMonths();
        
        foreach ($forecastMonths as $index => $month) {
            $trendValue = $trend['slope'] * (count($revenues) + $index + 1) + $trend['intercept'];
            $seasonalFactor = $seasonalFactors[$index % 12] ?? 1;
            $forecastValue = $trendValue * $seasonalFactor;
            
            $forecast[] = [
                'month' => $month,
                'revenue' => max(0, $forecastValue),
                'confidence_lower' => max(0, $forecastValue * 0.8),
                'confidence_upper' => $forecastValue * 1.2,
            ];
        }
        
        return $forecast;
    }

    private function calculateAppointmentForecast($historicalData)
    {
        $appointments = collect($historicalData)->pluck('appointments')->toArray();
        $forecast = [];
        
        $trend = $this->calculateLinearTrend($appointments);
        $seasonalFactors = $this->calculateSeasonalFactors($appointments);
        
        $forecastMonths = $this->getForecastMonths();
        
        foreach ($forecastMonths as $index => $month) {
            $trendValue = $trend['slope'] * (count($appointments) + $index + 1) + $trend['intercept'];
            $seasonalFactor = $seasonalFactors[$index % 12] ?? 1;
            $forecastValue = $trendValue * $seasonalFactor;
            
            $forecast[] = [
                'month' => $month,
                'appointments' => max(0, round($forecastValue)),
                'confidence_lower' => max(0, round($forecastValue * 0.85)),
                'confidence_upper' => round($forecastValue * 1.15),
            ];
        }
        
        return $forecast;
    }

    private function calculateLinearTrend($data)
    {
        $n = count($data);
        $sumX = array_sum(range(1, $n));
        $sumY = array_sum($data);
        $sumXY = 0;
        $sumXX = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($i + 1) * $data[$i];
            $sumXX += ($i + 1) * ($i + 1);
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        return ['slope' => $slope, 'intercept' => $intercept];
    }

    private function calculateSeasonalFactors($data)
    {
        $monthlyData = [];
        for ($i = 0; $i < count($data); $i++) {
            $monthIndex = $i % 12;
            if (!isset($monthlyData[$monthIndex])) {
                $monthlyData[$monthIndex] = [];
            }
            $monthlyData[$monthIndex][] = $data[$i];
        }
        
        $factors = [];
        $overallAverage = array_sum($data) / count($data);
        
        for ($i = 0; $i < 12; $i++) {
            if (isset($monthlyData[$i]) && count($monthlyData[$i]) > 0) {
                $monthAverage = array_sum($monthlyData[$i]) / count($monthlyData[$i]);
                $factors[$i] = $monthAverage / $overallAverage;
            } else {
                $factors[$i] = 1;
            }
        }
        
        return $factors;
    }

    private function getForecastMonths()
    {
        $months = [];
        $forecastCount = match($this->forecastPeriod) {
            '1_month' => 1,
            '3_months' => 3,
            '6_months' => 6,
            '12_months' => 12,
            default => 3,
        };
        
        for ($i = 1; $i <= $forecastCount; $i++) {
            $months[] = Carbon::now()->addMonths($i)->format('M Y');
        }
        
        return $months;
    }

    private function calculateForecastAccuracy($historicalData)
    {
        // Simple accuracy calculation based on recent variance
        $recentData = array_slice($historicalData, -6);
        $revenues = collect($recentData)->pluck('revenue')->toArray();
        
        $mean = array_sum($revenues) / count($revenues);
        $variance = array_sum(array_map(function($x) use ($mean) { 
            return pow($x - $mean, 2); 
        }, $revenues)) / count($revenues);
        
        $cv = sqrt($variance) / $mean;
        return max(0, min(100, 100 - ($cv * 30)));
    }

    private function calculateTrend($historicalData)
    {
        $revenues = collect($historicalData)->pluck('revenue')->toArray();
        $trend = $this->calculateLinearTrend($revenues);
        
        return [
            'direction' => $trend['slope'] > 0 ? 'upward' : ($trend['slope'] < 0 ? 'downward' : 'stable'),
            'strength' => abs($trend['slope']),
            'slope' => $trend['slope'],
        ];
    }

    private function calculateSeasonality($historicalData)
    {
        $revenues = collect($historicalData)->pluck('revenue')->toArray();
        $seasonalFactors = $this->calculateSeasonalFactors($revenues);
        
        $maxFactor = max($seasonalFactors);
        $minFactor = min($seasonalFactors);
        
        return [
            'strength' => ($maxFactor - $minFactor) / $maxFactor * 100,
            'peak_months' => array_keys($seasonalFactors, $maxFactor),
            'low_months' => array_keys($seasonalFactors, $minFactor),
        ];
    }

    private function identifyPeakPeriods($historicalData)
    {
        $appointments = collect($historicalData)->pluck('appointments')->toArray();
        $average = array_sum($appointments) / count($appointments);
        
        $peaks = [];
        foreach ($historicalData as $data) {
            if ($data['appointments'] > $average * 1.2) {
                $peaks[] = $data;
            }
        }
        
        return $peaks;
    }

    private function analyzeDemandPatterns($historicalData)
    {
        $patterns = [];
        
        // Analyze day-of-week patterns
        $dayPatterns = Appointment::selectRaw('DAYOFWEEK(appointment_date) as day_of_week, COUNT(*) as count')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('day_of_week')
            ->orderBy('count', 'desc')
            ->get();
        
        $patterns['peak_days'] = $dayPatterns->take(3)->pluck('day_of_week')->toArray();
        
        // Analyze hour patterns
        $hourPatterns = Appointment::selectRaw('HOUR(appointment_time) as hour, COUNT(*) as count')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
        
        $patterns['peak_hours'] = $hourPatterns->take(3)->pluck('hour')->toArray();
        
        return $patterns;
    }

    private function predictClientChurn()
    {
        $atRiskClients = Client::whereHas('appointments', function ($q) {
            $q->where('appointment_date', '<', Carbon::now()->subMonths(3))
              ->where('status', 'completed');
        })->whereDoesntHave('appointments', function ($q) {
            $q->where('appointment_date', '>=', Carbon::now()->subMonths(3))
              ->where('status', 'completed');
        })->count();
        
        $totalClients = Client::count();
        
        return [
            'at_risk_count' => $atRiskClients,
            'at_risk_percentage' => $totalClients > 0 ? ($atRiskClients / $totalClients) * 100 : 0,
            'retention_rate' => $totalClients > 0 ? (($totalClients - $atRiskClients) / $totalClients) * 100 : 0,
        ];
    }

    private function predictClientLifetimeValue()
    {
        $clients = Client::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed');
        }])->withSum('invoices', 'total_amount')->get();
        
        $avgCLV = $clients->avg('invoices_sum_total_amount');
        $avgAppointments = $clients->avg('appointments_count');
        
        return [
            'average_clv' => $avgCLV,
            'average_appointments' => $avgAppointments,
            'clv_trend' => $this->calculateCLVTrend($clients),
        ];
    }

    private function calculateCLVTrend($clients)
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
            
            $monthlyCLV[] = $clients > 0 ? $revenue / $clients : 0;
        }
        
        $trend = $this->calculateLinearTrend($monthlyCLV);
        return $trend['slope'] > 0 ? 'increasing' : ($trend['slope'] < 0 ? 'decreasing' : 'stable');
    }

    private function predictNextAppointment()
    {
        $clients = Client::with(['appointments' => function ($q) {
            $q->where('status', 'completed')->orderBy('appointment_date', 'desc');
        }])->get();
        
        $avgGap = $clients->map(function ($client) {
            $appointments = $client->appointments;
            if ($appointments->count() < 2) return null;
            
            $gaps = [];
            for ($i = 0; $i < $appointments->count() - 1; $i++) {
                $gaps[] = Carbon::parse($appointments[$i]->appointment_date)
                    ->diffInDays(Carbon::parse($appointments[$i + 1]->appointment_date));
            }
            
            return count($gaps) > 0 ? array_sum($gaps) / count($gaps) : null;
        })->filter()->avg();
        
        return [
            'average_gap_days' => round($avgGap),
            'next_appointment_probability' => $this->calculateNextAppointmentProbability(),
        ];
    }

    private function calculateNextAppointmentProbability()
    {
        $recentClients = Client::whereHas('appointments', function ($q) {
            $q->where('appointment_date', '>=', Carbon::now()->subMonths(3))
              ->where('status', 'completed');
        })->count();
        
        $totalClients = Client::count();
        
        return $totalClients > 0 ? ($recentClients / $totalClients) * 100 : 0;
    }

    private function generateServiceRecommendations()
    {
        $servicePerformance = Service::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed')
              ->where('appointment_date', '>=', Carbon::now()->subMonths(6));
        }])->get();
        
        $avgBookings = $servicePerformance->avg('appointments_count');
        
        return $servicePerformance->map(function ($service) use ($avgBookings) {
            $performance = $service->appointments_count / $avgBookings;
            return [
                'service' => $service,
                'performance_ratio' => $performance,
                'recommendation' => $performance > 1.2 ? 'Promote' : ($performance < 0.8 ? 'Review' : 'Maintain'),
            ];
        })->sortByDesc('performance_ratio');
    }

    private function predictStaffPerformanceTrends()
    {
        $staffPerformance = Staff::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed')
              ->where('appointment_date', '>=', Carbon::now()->subMonths(3));
        }])->get();
        
        return $staffPerformance->map(function ($staff) {
            return [
                'staff' => $staff,
                'current_performance' => $staff->appointments_count,
                'trend' => $this->calculateStaffTrend($staff),
                'predicted_performance' => $this->predictStaffPerformance($staff),
            ];
        });
    }

    private function calculateStaffTrend($staff)
    {
        $recentAppointments = $staff->appointments()
            ->where('status', 'completed')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(6))
            ->count();
        
        $olderAppointments = $staff->appointments()
            ->where('status', 'completed')
            ->whereBetween('appointment_date', [
                Carbon::now()->subMonths(12),
                Carbon::now()->subMonths(6)
            ])
            ->count();
        
        if ($olderAppointments == 0) return 'new';
        
        $trend = ($recentAppointments - $olderAppointments) / $olderAppointments;
        
        return $trend > 0.1 ? 'improving' : ($trend < -0.1 ? 'declining' : 'stable');
    }

    private function predictStaffPerformance($staff)
    {
        $currentPerformance = $staff->appointments()
            ->where('status', 'completed')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(3))
            ->count();
        
        $trend = $this->calculateStaffTrend($staff);
        
        $multiplier = match($trend) {
            'improving' => 1.1,
            'declining' => 0.9,
            'stable' => 1.0,
            default => 1.0,
        };
        
        return round($currentPerformance * $multiplier);
    }

    private function optimizeStaffCapacity()
    {
        $staffCount = Staff::count();
        $avgAppointmentsPerStaff = Appointment::where('status', 'completed')
            ->where('appointment_date', '>=', Carbon::now()->subMonths(3))
            ->count() / max($staffCount, 1);
        
        $optimalCapacity = $avgAppointmentsPerStaff * 1.2; // 20% buffer
        
        return [
            'current_capacity' => $avgAppointmentsPerStaff,
            'optimal_capacity' => $optimalCapacity,
            'capacity_utilization' => ($avgAppointmentsPerStaff / $optimalCapacity) * 100,
            'recommendation' => $avgAppointmentsPerStaff > $optimalCapacity ? 'Increase staff' : 'Optimize scheduling',
        ];
    }

    private function identifySkillGaps()
    {
        $serviceDemand = Service::withCount(['appointments' => function ($q) {
            $q->where('status', 'completed')
              ->where('appointment_date', '>=', Carbon::now()->subMonths(6));
        }])->get();
        
        $staffSkills = Staff::with('services')->get();
        
        $gaps = [];
        foreach ($serviceDemand as $service) {
            $qualifiedStaff = $staffSkills->filter(function ($staff) use ($service) {
                return $staff->services->contains('id', $service->id);
            })->count();
            
            if ($qualifiedStaff < 2 && $service->appointments_count > 10) {
                $gaps[] = [
                    'service' => $service,
                    'demand' => $service->appointments_count,
                    'qualified_staff' => $qualifiedStaff,
                    'priority' => 'high',
                ];
            }
        }
        
        return $gaps;
    }

    private function generateTrainingRecommendations()
    {
        $skillGaps = $this->identifySkillGaps();
        
        return collect($skillGaps)->map(function ($gap) {
            return [
                'service' => $gap['service'],
                'training_needed' => 'Staff training for ' . $gap['service']->name,
                'priority' => $gap['priority'],
                'estimated_impact' => 'High demand service',
            ];
        });
    }

    private function forecastInventoryDemand()
    {
        // This would typically integrate with inventory data
        // For now, return placeholder data
        return [
            'next_month_demand' => 150,
            'trend' => 'increasing',
            'confidence' => 85,
        ];
    }

    private function calculateReorderPoints()
    {
        return [
            'average_lead_time' => 7, // days
            'safety_stock' => 20,
            'reorder_point' => 50,
        ];
    }

    private function predictInventoryWaste()
    {
        return [
            'waste_percentage' => 5.2,
            'trend' => 'stable',
            'cost_impact' => 250,
        ];
    }

    private function optimizeInventoryCosts()
    {
        return [
            'current_costs' => 5000,
            'optimized_costs' => 4500,
            'savings_potential' => 500,
            'recommendations' => ['Bulk purchasing', 'Supplier negotiation'],
        ];
    }

    private function identifyGrowthOpportunities()
    {
        return [
            'service_expansion' => ['Spa treatments', 'Wellness programs'],
            'time_expansion' => ['Evening hours', 'Weekend services'],
            'client_segments' => ['Corporate clients', 'Group bookings'],
            'technology_upgrades' => ['Online booking', 'Mobile app'],
        ];
    }

    private function identifyRiskFactors()
    {
        return [
            'client_churn_risk' => $this->predictClientChurn()['at_risk_percentage'],
            'staff_turnover_risk' => 15, // Placeholder
            'seasonal_downturn' => 'Winter months',
            'competition_risk' => 'Medium',
        ];
    }

    private function analyzeMarketTrends()
    {
        return [
            'industry_growth' => 8.5,
            'consumer_preferences' => ['Wellness', 'Sustainability', 'Technology'],
            'market_size' => 'Growing',
            'opportunities' => ['Digital services', 'Eco-friendly products'],
        ];
    }

    private function performCompetitiveAnalysis()
    {
        return [
            'market_position' => 'Premium',
            'competitive_advantages' => ['Quality service', 'Experienced staff'],
            'areas_for_improvement' => ['Digital presence', 'Pricing strategy'],
            'threats' => ['New competitors', 'Economic downturn'],
        ];
    }

    public function render()
    {
        $revenueForecast = $this->getRevenueForecast();
        $appointmentForecast = $this->getAppointmentForecast();
        $clientPredictions = $this->getClientBehaviorPredictions();
        $staffPredictions = $this->getStaffPerformancePredictions();
        $inventoryPredictions = $this->getInventoryPredictions();
        $businessInsights = $this->getBusinessInsights();

        return view('livewire.admin.reports.predictive-analytics', [
            'revenueForecast' => $revenueForecast,
            'appointmentForecast' => $appointmentForecast,
            'clientPredictions' => $clientPredictions,
            'staffPredictions' => $staffPredictions,
            'inventoryPredictions' => $inventoryPredictions,
            'businessInsights' => $businessInsights,
        ])->layout('layouts.admin');
    }
}
