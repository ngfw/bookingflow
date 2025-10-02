<?php

namespace App\Livewire\Admin;

use App\Services\AnalyticsService;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public $selectedPeriod = '30d';
    public $selectedMetric = 'overview';
    public $startDate;
    public $endDate;

    protected $analyticsService;

    public function mount()
    {
        $this->analyticsService = new AnalyticsService();
        $this->setDateRange();
    }

    public function setDateRange()
    {
        switch ($this->selectedPeriod) {
            case '7d':
                $this->startDate = now()->subDays(7);
                $this->endDate = now();
                break;
            case '30d':
                $this->startDate = now()->subDays(30);
                $this->endDate = now();
                break;
            case '90d':
                $this->startDate = now()->subDays(90);
                $this->endDate = now();
                break;
            case '1y':
                $this->startDate = now()->subYear();
                $this->endDate = now();
                break;
            default:
                $this->startDate = now()->subDays(30);
                $this->endDate = now();
        }
    }

    public function updatedSelectedPeriod()
    {
        $this->setDateRange();
    }

    public function getDays()
    {
        return $this->startDate->diffInDays($this->endDate);
    }

    public function render()
    {
        $days = $this->getDays();
        
        $summary = $this->analyticsService->getAnalyticsSummary($days);
        $topPages = $this->analyticsService->getTopPages($days, 10);
        $trafficSources = $this->analyticsService->getTrafficSources($days);
        $deviceBreakdown = $this->analyticsService->getDeviceBreakdown($days);
        $locationBreakdown = $this->analyticsService->getLocationBreakdown($days);
        $dailyStats = $this->analyticsService->getDailyStats($days);
        $hourlyStats = $this->analyticsService->getHourlyStats(min($days, 7));
        $conversions = $this->analyticsService->getConversions($days);
        $realTimeVisitors = $this->analyticsService->getRealTimeVisitors(5);

        return view('livewire.admin.analytics-dashboard', [
            'summary' => $summary,
            'topPages' => $topPages,
            'trafficSources' => $trafficSources,
            'deviceBreakdown' => $deviceBreakdown,
            'locationBreakdown' => $locationBreakdown,
            'dailyStats' => $dailyStats,
            'hourlyStats' => $hourlyStats,
            'conversions' => $conversions,
            'realTimeVisitors' => $realTimeVisitors,
        ]);
    }
}
