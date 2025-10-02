<?php

namespace App\Livewire\Admin\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingCategory = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'required|string|max:7',
        'is_active' => 'boolean',
    ];

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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($categoryId)
    {
        $this->editingCategory = Category::findOrFail($categoryId);
        $this->name = $this->editingCategory->name;
        $this->description = $this->editingCategory->description;
        $this->color = $this->editingCategory->color;
        $this->is_active = $this->editingCategory->is_active;
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingCategory = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->color = '#3B82F6';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Category created successfully!');
        $this->closeModals();
    }

    public function updateCategory()
    {
        $this->validate();

        $this->editingCategory->update([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Category updated successfully!');
        $this->closeModals();
    }

    public function toggleCategoryStatus($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->update(['is_active' => !$category->is_active]);
        
        session()->flash('success', 'Category status updated successfully.');
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        // Check if category has services
        if ($category->services()->count() > 0) {
            session()->flash('error', 'Cannot delete category with existing services.');
            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $categories = Category::withCount('services')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
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

        return view('livewire.admin.categories.index', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
