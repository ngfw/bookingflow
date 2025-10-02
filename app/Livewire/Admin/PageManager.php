<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class PageManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus($pageId, $field = 'is_published')
    {
        $page = Page::findOrFail($pageId);
        $page->update([$field => !$page->$field]);
        session()->flash('success', 'Page status updated!');
    }

    public function setHomepage($pageId)
    {
        // Remove homepage flag from all pages
        Page::where('is_homepage', true)->update(['is_homepage' => false]);
        
        // Set new homepage
        $page = Page::findOrFail($pageId);
        $page->update(['is_homepage' => true]);
        
        session()->flash('success', 'Homepage updated successfully!');
    }

    public function deletePage($pageId)
    {
        $page = Page::findOrFail($pageId);
        
        // Don't allow deleting the homepage if it's the only page
        if ($page->is_homepage && Page::where('is_published', true)->count() <= 1) {
            session()->flash('error', 'Cannot delete the only published page. Please create another page first.');
            return;
        }
        
        $page->delete();
        session()->flash('success', 'Page deleted successfully!');
    }

    public function duplicatePage($pageId)
    {
        $page = Page::findOrFail($pageId);
        $newPage = $page->replicate();
        $newPage->title = $page->title . ' (Copy)';
        $newPage->slug = $page->slug . '-copy';
        $newPage->is_homepage = false;
        $newPage->is_published = false;
        $newPage->published_at = null;
        $newPage->save();

        // Duplicate sections
        foreach ($page->sections as $section) {
            $newSection = $section->replicate();
            $newSection->page_id = $newPage->id;
            $newSection->save();
        }

        session()->flash('success', 'Page duplicated successfully!');
    }

    public function render()
    {
        $pages = Page::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->filter === 'draft') {
                $query->where('is_published', false);
            } elseif ($this->filter === 'homepage') {
                $query->where('is_homepage', true);
            }
        })->orderBy($this->sortBy, $this->sortDirection)->paginate(12);

        return view('livewire.admin.page-manager', compact('pages'));
    }
}
