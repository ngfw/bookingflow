<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $staffFilter = '';
    public $sortField = 'appointment_date';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showFilters = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingStaffFilter()
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

    public function updateAppointmentStatus($appointmentId, $status)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => $status]);

        session()->flash('success', 'Appointment status updated successfully.');
    }

    public function deleteAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->delete();

        session()->flash('success', 'Appointment deleted successfully.');
    }

    public function render()
    {
        $appointments = Appointment::with(['client.user', 'staff.user', 'service'])
            ->when($this->search, function ($query) {
                $query->whereHas('client.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
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
            ->when($this->staffFilter, function ($query) {
                $query->where('staff_id', $this->staffFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        return view('livewire.admin.appointments.index', [
            'appointments' => $appointments,
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
