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
        $this->stats = [
            'total_clients' => Client::count(),
            'total_staff' => Staff::count(),
            'total_services' => Service::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', Carbon::today())->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'monthly_revenue' => Invoice::whereMonth('created_at', Carbon::now()->month)->sum('total_amount'),
            'upcoming_appointments' => Appointment::where('appointment_date', '>=', Carbon::now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('appointment_date')
                ->limit(5)
                ->get(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.admin');
    }
}
