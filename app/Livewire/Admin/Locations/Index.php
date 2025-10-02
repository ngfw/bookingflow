<?php

namespace App\Livewire\Admin\Locations;

use Livewire\Component;
use App\Models\Location;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showFilters = false;
    
    // Filters
    public $statusFilter = '';
    public $cityFilter = '';
    public $stateFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->cityFilter = '';
        $this->stateFilter = '';
        $this->resetPage();
    }

    public function deleteLocation($locationId)
    {
        $location = Location::findOrFail($locationId);
        
        // Check if location has any associated data
        if ($location->appointments()->count() > 0) {
            session()->flash('error', 'Cannot delete location with existing appointments.');
            return;
        }
        
        if ($location->staff()->count() > 0) {
            session()->flash('error', 'Cannot delete location with assigned staff.');
            return;
        }

        $location->delete();
        session()->flash('success', 'Location deleted successfully.');
    }

    public function toggleLocationStatus($locationId)
    {
        $location = Location::findOrFail($locationId);
        $location->update(['is_active' => !$location->is_active]);
        
        $status = $location->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Location {$status} successfully.");
    }

    public function render()
    {
        $locations = Location::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('city', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', 'like', '%' . $this->cityFilter . '%');
            })
            ->when($this->stateFilter, function ($query) {
                $query->where('state', 'like', '%' . $this->stateFilter . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total_locations' => Location::count(),
            'active_locations' => Location::where('is_active', true)->count(),
            'inactive_locations' => Location::where('is_active', false)->count(),
            'headquarters' => Location::where('is_headquarters', true)->count(),
        ];

        return view('livewire.admin.locations.index', compact('locations', 'stats'));
    }
}