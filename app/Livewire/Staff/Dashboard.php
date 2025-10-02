<?php

namespace App\Livewire\Staff;

use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Schedule;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.staff')]
class Dashboard extends Component
{
    public $staff;
    public $todayAppointments;
    public $upcomingAppointments;
    public $todaysSchedule;
    public $stats;
    public $notifications;
    public $currentTime;

    public function mount()
    {
        $this->staff = Staff::where('user_id', auth()->id())->first();
        
        if (!$this->staff) {
            // If no staff record exists, create one
            $this->staff = Staff::create([
                'user_id' => auth()->id(),
                'specialization' => 'General',
                'commission_rate' => 0.4,
                'hire_date' => now(),
                'employment_status' => 'active',
            ]);
        }

        $this->loadDashboardData();
        $this->currentTime = now()->format('g:i A');
    }

    public function loadDashboardData()
    {
        $today = Carbon::today();
        
        // Today's appointments
        $this->todayAppointments = Appointment::where('staff_id', $this->staff->id)
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in', 'in_progress'])
            ->with(['client.user', 'service'])
            ->orderBy('appointment_date')
            ->get();

        // Upcoming appointments (next 7 days)
        $this->upcomingAppointments = Appointment::where('staff_id', $this->staff->id)
            ->whereBetween('appointment_date', [
                $today->copy()->addDay(),
                $today->copy()->addDays(7)
            ])
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['client.user', 'service'])
            ->orderBy('appointment_date')
            ->limit(5)
            ->get();

        // Today's schedule
        $this->todaysSchedule = Schedule::where('staff_id', $this->staff->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        // Calculate stats
        $this->calculateStats();
        
        // Load notifications
        $this->loadNotifications();
    }

    public function calculateStats()
    {
        $today = Carbon::today();
        $thisWeek = [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()];
        $thisMonth = [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];

        $this->stats = [
            'today' => [
                'appointments' => $this->todayAppointments->count(),
                'completed' => Appointment::where('staff_id', $this->staff->id)
                    ->whereDate('appointment_date', $today)
                    ->where('status', 'completed')
                    ->count(),
                'revenue' => Appointment::where('staff_id', $this->staff->id)
                    ->whereDate('appointment_date', $today)
                    ->where('status', 'completed')
                    ->with('service')
                    ->get()
                    ->sum('service.price'),
                'next_appointment' => $this->getNextAppointment(),
            ],
            'week' => [
                'appointments' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisWeek)
                    ->count(),
                'completed' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisWeek)
                    ->where('status', 'completed')
                    ->count(),
                'revenue' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisWeek)
                    ->where('status', 'completed')
                    ->with('service')
                    ->get()
                    ->sum('service.price'),
            ],
            'month' => [
                'appointments' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisMonth)
                    ->count(),
                'completed' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisMonth)
                    ->where('status', 'completed')
                    ->count(),
                'revenue' => Appointment::where('staff_id', $this->staff->id)
                    ->whereBetween('appointment_date', $thisMonth)
                    ->where('status', 'completed')
                    ->with('service')
                    ->get()
                    ->sum('service.price'),
            ],
        ];
    }

    public function getNextAppointment()
    {
        return Appointment::where('staff_id', $this->staff->id)
            ->where('appointment_date', '>', now())
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->with(['client.user', 'service'])
            ->orderBy('appointment_date')
            ->first();
    }

    public function loadNotifications()
    {
        $this->notifications = [
            [
                'type' => 'info',
                'message' => 'Remember to update your availability for next week',
                'time' => '2 hours ago',
                'icon' => 'calendar'
            ],
            [
                'type' => 'success',
                'message' => 'Client Sarah Johnson left a 5-star review!',
                'time' => '1 day ago',
                'icon' => 'star'
            ],
            [
                'type' => 'warning',
                'message' => 'You have 3 pending appointment requests',
                'time' => '2 days ago',
                'icon' => 'clock'
            ],
        ];
    }

    public function markAppointmentCompleted($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('staff_id', $this->staff->id)
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'completed']);
            $this->loadDashboardData(); // Refresh data
            session()->flash('message', 'Appointment marked as completed!');
        }
    }

    public function startAppointment($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('staff_id', $this->staff->id)
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'in_progress']);
            $this->loadDashboardData(); // Refresh data
            session()->flash('message', 'Appointment started!');
        }
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->currentTime = now()->format('g:i A');
    }

    public function render()
    {
        return view('livewire.staff.dashboard');
    }
}