<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Staff;
use App\Models\User;
use App\Models\Service;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStaffStatus($staffId)
    {
        $staff = Staff::findOrFail($staffId);
        $staff->user->update(['is_active' => !$staff->user->is_active]);

        session()->flash('success', 'Staff status updated successfully.');
    }

    public function deleteStaff($staffId)
    {
        $staff = Staff::findOrFail($staffId);

        if ($staff->appointments()->count() > 0) {
            session()->flash('error', 'Cannot delete staff with existing appointments.');
            return;
        }

        $staff->user()->delete();
        session()->flash('success', 'Staff deleted successfully.');
    }

    public function render()
    {
        $staff = Staff::with(['user', 'services', 'appointments'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->whereHas('user', function ($q) {
                        $q->where('is_active', true);
                    });
                } elseif ($this->statusFilter === 'inactive') {
                    $query->whereHas('user', function ($q) {
                        $q->where('is_active', false);
                    });
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.staff.index', [
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
