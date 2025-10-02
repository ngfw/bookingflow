<?php

namespace App\Livewire\Admin\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Page;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($pageId)
    {
        $page = Page::findOrFail($pageId);
        $page->delete();

        session()->flash('message', 'Page deleted successfully.');
    }

    public function togglePublish($pageId)
    {
        $page = Page::findOrFail($pageId);
        $page->update(['is_published' => !$page->is_published]);

        session()->flash('message', 'Page status updated successfully.');
    }

    public function setHomepage($pageId)
    {
        // First, remove homepage status from all pages
        Page::where('is_homepage', true)->update(['is_homepage' => false]);
        
        // Set the selected page as homepage
        $page = Page::findOrFail($pageId);
        $page->update(['is_homepage' => true, 'is_published' => true]);

        session()->flash('message', 'Homepage set successfully.');
    }

    public function reorderPages($pageIds)
    {
        foreach ($pageIds as $order => $pageId) {
            Page::where('id', $pageId)->update(['sort_order' => $order + 1]);
        }

        session()->flash('message', 'Pages reordered successfully.');
    }

    public function render()
    {
        $query = Page::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            if ($this->statusFilter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->statusFilter === 'draft') {
                $query->where('is_published', false);
            } elseif ($this->statusFilter === 'homepage') {
                $query->where('is_homepage', true);
            }
        }

        $pages = $query->orderBy($this->sortBy, $this->sortDirection)
                      ->paginate(20);

        return view('livewire.admin.pages.index', [
            'pages' => $pages,
        ])->layout('layouts.admin');
    }
}