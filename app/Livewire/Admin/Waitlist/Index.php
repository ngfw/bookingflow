<?php

namespace App\Livewire\Admin\Waitlist;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Waitlist;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $serviceFilter = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingServiceFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
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

    public function updateStatus($waitlistId, $status)
    {
        $waitlist = Waitlist::findOrFail($waitlistId);
        
        $updateData = ['status' => $status];
        
        if ($status === 'contacted') {
            $updateData['contacted_at'] = Carbon::now();
        }
        
        $waitlist->update($updateData);

        session()->flash('success', 'Waitlist status updated successfully.');
    }

    public function deleteWaitlist($waitlistId)
    {
        $waitlist = Waitlist::findOrFail($waitlistId);
        $waitlist->delete();

        session()->flash('success', 'Waitlist entry deleted successfully.');
    }

    public function convertToAppointment($waitlistId)
    {
        $waitlist = Waitlist::with(['client', 'service', 'staff'])->findOrFail($waitlistId);
        
        // This would redirect to appointment creation with pre-filled data
        // For now, we'll just show a message
        session()->flash('success', "Ready to create appointment for {$waitlist->client->user->name}. Redirecting to appointment creation...");
        
        // In a real implementation, you would redirect to appointment creation
        // with the waitlist data pre-filled
        return redirect()->route('admin.appointments.create', [
            'client_id' => $waitlist->client_id,
            'service_id' => $waitlist->service_id,
            'staff_id' => $waitlist->staff_id,
            'appointment_date' => $waitlist->preferred_date->format('Y-m-d'),
            'appointment_time' => $waitlist->preferred_time_start->format('H:i'),
        ]);
    }

    public function render()
    {
        $waitlist = Waitlist::with(['client.user', 'service', 'staff.user'])
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
            ->when($this->serviceFilter, function ($query) {
                $query->where('service_id', $this->serviceFilter);
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('preferred_date', Carbon::today());
                } elseif ($this->dateFilter === 'tomorrow') {
                    $query->whereDate('preferred_date', Carbon::tomorrow());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('preferred_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'next_week') {
                    $query->whereBetween('preferred_date', [Carbon::now()->addWeek()->startOfWeek(), Carbon::now()->addWeek()->endOfWeek()]);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.waitlist.index', [
            'waitlist' => $waitlist,
            'services' => $services,
        ])->layout('layouts.admin');
    }
}
