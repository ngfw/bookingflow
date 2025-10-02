<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\PageSection;
use Livewire\Component;
use Illuminate\Support\Str;

class PageEditor extends Component
{
    public $page;
    public $title;
    public $slug;
    public $excerpt;
    public $meta_title;
    public $meta_description;
    public $is_published = false;
    public $is_homepage = false;
    public $published_at;
    public $sections = [];
    public $newSectionType = 'hero';
    public $editingSection = null;
    public $showSectionEditor = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug',
        'excerpt' => 'nullable|string|max:500',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'is_published' => 'boolean',
        'is_homepage' => 'boolean',
        'published_at' => 'nullable|date',
    ];

    public function mount($pageId = null)
    {
        if ($pageId) {
            $this->page = Page::with('sections')->findOrFail($pageId);
            $this->loadPageData();
        } else {
            $this->page = new Page();
            $this->published_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function loadPageData()
    {
        $this->title = $this->page->title;
        $this->slug = $this->page->slug;
        $this->excerpt = $this->page->excerpt;
        $this->meta_title = $this->page->meta_title;
        $this->meta_description = $this->page->meta_description;
        $this->is_published = $this->page->is_published;
        $this->is_homepage = $this->page->is_homepage;
        $this->published_at = $this->page->published_at ? $this->page->published_at->format('Y-m-d\TH:i') : null;
        $this->sections = $this->page->sections->toArray();
    }

    public function updatedTitle()
    {
        if (!$this->page->exists || $this->slug === Str::slug($this->page->title)) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedSlug()
    {
        $this->slug = Str::slug($this->slug);
    }

    public function save()
    {
        $this->validate();

        // If setting as homepage, remove homepage flag from other pages
        if ($this->is_homepage) {
            Page::where('is_homepage', true)->where('id', '!=', $this->page->id ?? 0)->update(['is_homepage' => false]);
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_published' => $this->is_published,
            'is_homepage' => $this->is_homepage,
            'published_at' => $this->published_at ? now()->parse($this->published_at) : null,
        ];

        if ($this->page->exists) {
            $this->page->update($data);
        } else {
            $this->page = Page::create($data);
        }

        session()->flash('success', 'Page saved successfully!');
        return redirect()->route('admin.pages.edit', $this->page->id);
    }

    public function addSection()
    {
        $this->editingSection = null;
        $this->showSectionEditor = true;
    }

    public function editSection($sectionId)
    {
        $this->editingSection = $sectionId;
        $this->showSectionEditor = true;
    }

    public function deleteSection($sectionId)
    {
        if ($this->page->exists) {
            PageSection::where('id', $sectionId)->where('page_id', $this->page->id)->delete();
        }
        
        $this->sections = array_filter($this->sections, function($section) use ($sectionId) {
            return $section['id'] != $sectionId;
        });
        
        session()->flash('success', 'Section deleted successfully!');
    }

    public function moveSectionUp($index)
    {
        if ($index > 0) {
            $temp = $this->sections[$index];
            $this->sections[$index] = $this->sections[$index - 1];
            $this->sections[$index - 1] = $temp;
        }
    }

    public function moveSectionDown($index)
    {
        if ($index < count($this->sections) - 1) {
            $temp = $this->sections[$index];
            $this->sections[$index] = $this->sections[$index + 1];
            $this->sections[$index + 1] = $temp;
        }
    }

    public function preview()
    {
        if (!$this->page->exists) {
            $this->save();
        }
        
        return redirect()->route('pages.show', $this->page->slug);
    }

    public function render()
    {
        return view('livewire.admin.page-editor');
    }
}
