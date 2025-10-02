<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\Service;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\Staff;
use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class PageBuilder extends Component
{
    use WithFileUploads;

    public $page;
    public $sections = [];
    public $availableSections = [];
    public $selectedSection = null;
    public $editingSection = null;
    public $showSectionModal = false;
    public $showPageModal = false;
    public $pageData = [];
    public $sectionData = [];
    public $draggedSection = null;
    public $previewMode = false;

    // Page form fields
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $template = 'default';
    public $is_published = false;
    public $is_homepage = false;
    public $featured_image;

    // Section form fields
    public $section_type = '';
    public $section_title = '';
    public $section_content = '';
    public $section_settings = [];
    public $section_media = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug',
        'excerpt' => 'nullable|string',
        'content' => 'nullable|string',
        'template' => 'required|string',
        'is_published' => 'boolean',
        'is_homepage' => 'boolean',
        'featured_image' => 'nullable|image|max:2048',
    ];

    public function mount($pageId = null)
    {
        if ($pageId) {
            $this->page = Page::findOrFail($pageId);
            $this->loadPageData();
        } else {
            $this->page = new Page();
        }

        $this->loadSections();
        $this->loadAvailableSections();
    }

    public function loadPageData()
    {
        $this->title = $this->page->title;
        $this->slug = $this->page->slug;
        $this->excerpt = $this->page->excerpt;
        $this->content = $this->page->content;
        $this->template = $this->page->template;
        $this->is_published = $this->page->is_published;
        $this->is_homepage = $this->page->is_homepage;
    }

    public function loadSections()
    {
        if ($this->page->id) {
            $this->sections = $this->page->sections()->orderBy('sort_order')->get()->toArray();
        }
    }

    public function loadAvailableSections()
    {
        $this->availableSections = PageSection::getSectionTypes();
    }

    public function savePage()
    {
        $this->validate();

        // Handle featured image upload
        if ($this->featured_image) {
            $path = $this->featured_image->store('pages', 'public');
            $this->page->featured_image = $path;
        }

        $this->page->fill([
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'template' => $this->template,
            'is_published' => $this->is_published,
            'is_homepage' => $this->is_homepage,
        ]);

        if (!$this->page->id) {
            $this->page->save();
            return redirect()->route('admin.page-builder', $this->page->id);
        }

        $this->page->save();
        $this->loadSections();
        $this->showPageModal = false;
        session()->flash('success', 'Page saved successfully!');
    }

    public function addSection()
    {
        $this->resetSectionForm();
        $this->showSectionModal = true;
    }

    public function editSection($sectionId)
    {
        $section = PageSection::findOrFail($sectionId);
        $this->editingSection = $section;
        $this->section_type = $section->section_type;
        $this->section_title = $section->title;
        $this->section_content = $section->content;
        $this->section_settings = $section->settings ?? [];
        $this->section_media = $section->media ?? [];
        $this->showSectionModal = true;
    }

    public function saveSection()
    {
        $this->validate([
            'section_type' => 'required|string',
            'section_title' => 'nullable|string|max:255',
            'section_content' => 'nullable|string',
        ]);

        $data = [
            'page_id' => $this->page->id,
            'section_type' => $this->section_type,
            'title' => $this->section_title,
            'content' => $this->section_content,
            'settings' => $this->section_settings,
            'media' => $this->section_media,
            'sort_order' => count($this->sections),
        ];

        if ($this->editingSection) {
            $this->editingSection->update($data);
        } else {
            PageSection::create($data);
        }

        $this->loadSections();
        $this->showSectionModal = false;
        $this->resetSectionForm();
        session()->flash('success', 'Section saved successfully!');
    }

    public function deleteSection($sectionId)
    {
        PageSection::findOrFail($sectionId)->delete();
        $this->loadSections();
        session()->flash('success', 'Section deleted successfully!');
    }

    public function duplicateSection($sectionId)
    {
        $section = PageSection::findOrFail($sectionId);
        $newSection = $section->replicate();
        $newSection->title = $section->title . ' (Copy)';
        $newSection->sort_order = count($this->sections);
        $newSection->save();

        $this->loadSections();
        session()->flash('success', 'Section duplicated successfully!');
    }

    public function reorderSections($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            PageSection::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->loadSections();
    }

    public function toggleSectionVisibility($sectionId)
    {
        $section = PageSection::findOrFail($sectionId);
        $section->update(['is_active' => !$section->is_active]);
        $this->loadSections();
    }

    public function resetSectionForm()
    {
        $this->editingSection = null;
        $this->section_type = '';
        $this->section_title = '';
        $this->section_content = '';
        $this->section_settings = [];
        $this->section_media = [];
    }

    public function togglePreview()
    {
        $this->previewMode = !$this->previewMode;
    }

    public function getSectionData($sectionType)
    {
        switch ($sectionType) {
            case 'services':
                return Service::where('is_active', true)->get();
            case 'gallery':
                return Gallery::where('is_active', true)->get();
            case 'testimonials':
                return Testimonial::where('is_active', true)->get();
            case 'team':
                return Staff::where('is_active', true)->get();
            case 'blog':
                return BlogPost::where('is_published', true)->limit(3)->get();
            default:
                return [];
        }
    }

    public function render()
    {
        return view('livewire.admin.page-builder', [
            'sectionData' => $this->getSectionData($this->section_type),
        ]);
    }
}
