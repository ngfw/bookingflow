<?php

namespace App\Livewire\Admin\Services;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\Category;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
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

    public function toggleServiceStatus($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->update(['is_active' => !$service->is_active]);
        
        session()->flash('success', 'Service status updated successfully.');
    }

    public function deleteService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        // Check if service has appointments
        if ($service->appointments()->count() > 0) {
            session()->flash('error', 'Cannot delete service with existing appointments.');
            return;
        }

        $service->delete();
        session()->flash('success', 'Service deleted successfully.');
    }

    public function render()
    {
        $services = Service::with(['category', 'staff'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.services.index', [
            'services' => $services,
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
