<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use Carbon\Carbon;

class Reminders extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFilter = '';
    public $reminderFilter = '';
    public $sortField = 'appointment_date';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingReminderFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function sendReminder($appointmentId)
    {
        $appointment = Appointment::with(['client.user', 'staff.user', 'service'])->findOrFail($appointmentId);
        
        // Update reminder status
        $appointment->update([
            'reminder_sent' => true,
            'reminder_sent_at' => Carbon::now(),
        ]);

        // Here you would typically send an email or SMS
        // For now, we'll just show a success message
        session()->flash('success', "Reminder sent to {$appointment->client->user->name} for appointment on " . Carbon::parse($appointment->appointment_date)->format('M j, Y g:i A'));
    }

    public function sendBulkReminders()
    {
        $appointments = $this->getAppointmentsQuery()->get();
        $sentCount = 0;

        foreach ($appointments as $appointment) {
            if (!$appointment->reminder_sent) {
                $appointment->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => Carbon::now(),
                ]);
                $sentCount++;
            }
        }

        session()->flash('success', "Bulk reminders sent to {$sentCount} clients.");
    }

    public function getAppointmentsQuery()
    {
        return Appointment::with(['client.user', 'staff.user', 'service'])
            ->when($this->search, function ($query) {
                $query->whereHas('client.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('appointment_date', Carbon::today());
                } elseif ($this->dateFilter === 'tomorrow') {
                    $query->whereDate('appointment_date', Carbon::tomorrow());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('appointment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'next_week') {
                    $query->whereBetween('appointment_date', [Carbon::now()->addWeek()->startOfWeek(), Carbon::now()->addWeek()->endOfWeek()]);
                }
            })
            ->when($this->reminderFilter, function ($query) {
                if ($this->reminderFilter === 'not_sent') {
                    $query->where('reminder_sent', false);
                } elseif ($this->reminderFilter === 'sent') {
                    $query->where('reminder_sent', true);
                }
            })
            ->where('status', '!=', 'cancelled')
            ->where('appointment_date', '>=', Carbon::now())
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $appointments = $this->getAppointmentsQuery()->paginate($this->perPage);

        return view('livewire.admin.appointments.reminders', [
            'appointments' => $appointments,
        ])->layout('layouts.admin');
    }
}
