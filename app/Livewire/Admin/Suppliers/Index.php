<?php

namespace App\Livewire\Admin\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

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

    public function deleteSupplier($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        // Check if supplier has products
        if ($supplier->products()->count() > 0) {
            session()->flash('error', 'Cannot delete supplier with associated products. Please reassign products first.');
            return;
        }
        
        $supplier->delete();
        session()->flash('success', 'Supplier deleted successfully.');
    }

    public function toggleSupplierStatus($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->update(['is_active' => !$supplier->is_active]);
        
        $status = $supplier->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Supplier {$status} successfully.");
    }

    public function render()
    {
        $suppliers = Supplier::withCount('products')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
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

        return view('livewire.admin.suppliers.index', [
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }
}
