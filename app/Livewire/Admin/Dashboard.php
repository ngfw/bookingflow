<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Invoice;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        $this->stats = [
            'total_clients' => Client::count(),
            'total_staff' => Staff::count(),
            'total_services' => Service::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'weekly_appointments' => Appointment::where('appointment_date', '>=', $thisWeek)->count(),
            'monthly_revenue' => Invoice::whereMonth('created_at', Carbon::now()->month)->sum('total_amount'),
            'last_month_revenue' => Invoice::whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total_amount'),
            'weekly_revenue' => Invoice::where('created_at', '>=', $thisWeek)->sum('total_amount'),
            'daily_revenue' => Invoice::whereDate('created_at', $today)->sum('total_amount'),
            'new_clients_this_month' => Client::whereMonth('created_at', Carbon::now()->month)->count(),
            'upcoming_appointments' => Appointment::where('appointment_date', '>=', Carbon::now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('appointment_date')
                ->with(['client.user', 'service', 'staff'])
                ->limit(10)
                ->get(),
            'recent_clients' => Client::with('user')->latest()->limit(5)->get(),
            'top_services' => Service::withCount('appointments')
                ->orderBy('appointments_count', 'desc')
                ->limit(5)
                ->get(),
        ];
        
        // Calculate revenue growth
        $currentRevenue = $this->stats['monthly_revenue'];
        $lastRevenue = $this->stats['last_month_revenue'];
        $this->stats['revenue_growth'] = $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.admin');
    }
}
