<?php

namespace App\Livewire\Staff;

use App\Models\Appointment;
use App\Models\Staff;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.staff')]
class Appointments extends Component
{
    use WithPagination;

    public $staff;
    public $selectedDate;
    public $statusFilter = 'all';
    public $search = '';

    public function mount()
    {
        $this->staff = Staff::where('user_id', auth()->id())->first();
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function getAppointmentsProperty()
    {
        $query = Appointment::where('staff_id', $this->staff->id)
            ->with(['client.user', 'service']);

        if ($this->selectedDate) {
            $query->whereDate('appointment_date', $this->selectedDate);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->whereHas('client.user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('appointment_date', 'asc')->paginate(10);
    }

    public function startAppointment($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('staff_id', $this->staff->id)
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'in_progress']);
            session()->flash('message', 'Appointment started!');
        }
    }

    public function completeAppointment($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('staff_id', $this->staff->id)
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'completed']);
            session()->flash('message', 'Appointment completed!');
        }
    }

    public function render()
    {
        return view('livewire.staff.appointments', [
            'appointments' => $this->appointments
        ]);
    }
}