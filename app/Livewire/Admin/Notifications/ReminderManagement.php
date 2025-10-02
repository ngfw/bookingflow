<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReminderSchedule;
use App\Models\Appointment;
use App\Models\NotificationLog;
use Carbon\Carbon;

class ReminderManagement extends Component
{
    use WithPagination;

    public $showScheduleModal = false;
    public $showTestModal = false;
    public $selectedSchedule = null;
    public $search = '';
    public $statusFilter = 'all';

    // Schedule form fields
    public $name, $description, $hours_before, $notification_types = [], $conditions = [], $is_active = true, $is_default = false, $priority = 1;

    // Test reminder fields
    public $testAppointmentId, $testScheduleId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'hours_before' => 'required|integer|min:1|max:8760', // Max 1 year
        'notification_types' => 'required|array|min:1',
        'conditions' => 'nullable|array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'priority' => 'required|integer|min:1',
    ];

    public function mount()
    {
        // Create default schedules if they don't exist
        ReminderSchedule::createDefaultSchedules();
    }

    public function getSchedules()
    {
        $query = ReminderSchedule::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('priority')->orderBy('hours_before', 'desc')->paginate(10);
    }

    public function getUpcomingReminders()
    {
        $now = Carbon::now();
        $next24Hours = $now->copy()->addDay();

        return Appointment::with(['client', 'service', 'staff'])
            ->where('status', 'confirmed')
            ->whereBetween('appointment_date', [$now, $next24Hours])
            ->orderBy('appointment_date')
            ->limit(20)
            ->get();
    }

    public function getReminderStats()
    {
        $today = Carbon::today();
        $week = Carbon::now()->subWeek();
        $month = Carbon::now()->subMonth();

        return [
            'today' => [
                'sent' => NotificationLog::where('event', 'appointment_reminder')
                    ->whereDate('created_at', $today)
                    ->count(),
                'delivered' => NotificationLog::where('event', 'appointment_reminder')
                    ->where('status', 'delivered')
                    ->whereDate('created_at', $today)
                    ->count(),
            ],
            'week' => [
                'sent' => NotificationLog::where('event', 'appointment_reminder')
                    ->where('created_at', '>=', $week)
                    ->count(),
                'delivered' => NotificationLog::where('event', 'appointment_reminder')
                    ->where('status', 'delivered')
                    ->where('created_at', '>=', $week)
                    ->count(),
            ],
            'month' => [
                'sent' => NotificationLog::where('event', 'appointment_reminder')
                    ->where('created_at', '>=', $month)
                    ->count(),
                'delivered' => NotificationLog::where('event', 'appointment_reminder')
                    ->where('status', 'delivered')
                    ->where('created_at', '>=', $month)
                    ->count(),
            ],
        ];
    }

    public function createSchedule()
    {
        $this->resetInputFields();
        $this->showScheduleModal = true;
    }

    public function editSchedule(ReminderSchedule $schedule)
    {
        $this->selectedSchedule = $schedule;
        $this->name = $schedule->name;
        $this->description = $schedule->description;
        $this->hours_before = $schedule->hours_before;
        $this->notification_types = $schedule->notification_types;
        $this->conditions = $schedule->conditions ?? [];
        $this->is_active = $schedule->is_active;
        $this->is_default = $schedule->is_default;
        $this->priority = $schedule->priority;
        $this->showScheduleModal = true;
    }

    public function storeSchedule()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'hours_before' => $this->hours_before,
            'notification_types' => $this->notification_types,
            'conditions' => $this->conditions,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'priority' => $this->priority,
        ];

        if ($this->selectedSchedule) {
            $this->selectedSchedule->update($data);
            session()->flash('message', 'Schedule updated successfully.');
        } else {
            ReminderSchedule::create($data);
            session()->flash('message', 'Schedule created successfully.');
        }

        $this->closeScheduleModal();
    }

    public function deleteSchedule(ReminderSchedule $schedule)
    {
        if ($schedule->is_default) {
            session()->flash('error', 'Cannot delete default schedule.');
            return;
        }

        $schedule->delete();
        session()->flash('message', 'Schedule deleted successfully.');
    }

    public function toggleScheduleStatus(ReminderSchedule $schedule)
    {
        $schedule->update(['is_active' => !$schedule->is_active]);
        session()->flash('message', 'Schedule status updated.');
    }

    public function testReminder()
    {
        $this->validate([
            'testAppointmentId' => 'required|exists:appointments,id',
            'testScheduleId' => 'required|exists:reminder_schedules,id',
        ]);

        try {
            $appointment = Appointment::with(['client', 'service', 'staff'])->find($this->testAppointmentId);
            $schedule = ReminderSchedule::find($this->testScheduleId);

            // Run the reminder command for this specific appointment
            \Artisan::call('appointments:send-reminders', [
                '--schedule' => $schedule->id,
                '--dry-run' => false,
            ]);

            session()->flash('message', 'Test reminder sent successfully.');
            $this->closeTestModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Error sending test reminder: ' . $e->getMessage());
        }
    }

    public function runReminderCommand()
    {
        try {
            \Artisan::call('appointments:send-reminders');
            $output = \Artisan::output();
            
            session()->flash('message', 'Reminder command executed successfully.');
            $this->dispatch('show-command-output', output: $output);

        } catch (\Exception $e) {
            session()->flash('error', 'Error running reminder command: ' . $e->getMessage());
        }
    }

    public function runDryRun()
    {
        try {
            \Artisan::call('appointments:send-reminders', ['--dry-run' => true]);
            $output = \Artisan::output();
            
            session()->flash('message', 'Dry run completed successfully.');
            $this->dispatch('show-command-output', output: $output);

        } catch (\Exception $e) {
            session()->flash('error', 'Error running dry run: ' . $e->getMessage());
        }
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->resetInputFields();
    }

    public function closeTestModal()
    {
        $this->showTestModal = false;
        $this->reset(['testAppointmentId', 'testScheduleId']);
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->hours_before = 24;
        $this->notification_types = ['email'];
        $this->conditions = [];
        $this->is_active = true;
        $this->is_default = false;
        $this->priority = 1;
        $this->selectedSchedule = null;
    }

    public function getAppointments()
    {
        return Appointment::with(['client', 'service', 'staff'])
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', Carbon::now())
            ->orderBy('appointment_date')
            ->limit(50)
            ->get();
    }

    public function render()
    {
        $schedules = $this->getSchedules();
        $upcomingReminders = $this->getUpcomingReminders();
        $stats = $this->getReminderStats();
        $appointments = $this->getAppointments();

        return view('livewire.admin.notifications.reminder-management', [
            'schedules' => $schedules,
            'upcomingReminders' => $upcomingReminders,
            'stats' => $stats,
            'appointments' => $appointments,
        ])->layout('layouts.admin');
    }
}
