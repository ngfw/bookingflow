<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
class EnhancedDashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $this->stats = [
            'total_clients' => Client::count(),
            'total_staff' => Staff::count(),
            'total_services' => Service::count(),
            'monthly_revenue' => Appointment::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_price'),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'completed_today' => Appointment::where('status', 'completed')
                ->whereDate('appointment_date', $today)
                ->count(),
            'upcoming_appointments' => Appointment::with(['client.user', 'service', 'staff.user'])
                ->whereDate('appointment_date', $today)
                ->where('status', '!=', 'cancelled')
                ->orderBy('appointment_date')
                ->get()
        ];
    }

    public function render()
    {
        return view('livewire.admin.enhanced-dashboard');
    }
}
