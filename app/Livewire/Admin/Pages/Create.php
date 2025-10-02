<?php

namespace App\Livewire\Admin\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Page;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $template = 'default';
    public $featured_image;
    public $is_published = false;
    public $is_homepage = false;
    public $sort_order = 0;
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';

    protected $rules = [
        'title' => 'required|max:255',
        'slug' => 'required|max:255|unique:pages,slug',
        'excerpt' => 'nullable|max:500',
        'content' => 'nullable',
        'template' => 'required',
        'featured_image' => 'nullable|image|max:2048',
        'is_published' => 'boolean',
        'is_homepage' => 'boolean',
        'sort_order' => 'integer|min:0',
        'meta_title' => 'nullable|max:60',
        'meta_description' => 'nullable|max:160',
        'meta_keywords' => 'nullable|max:255',
    ];

    public function updatedTitle()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->title);
    }

    public function save()
    {
        $this->validate();

        // If setting as homepage, remove homepage status from other pages
        if ($this->is_homepage) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
            $this->is_published = true; // Homepage must be published
        }

        $pageData = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'template' => $this->template,
            'is_published' => $this->is_published,
            'is_homepage' => $this->is_homepage,
            'sort_order' => $this->sort_order,
            'meta_data' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ],
            'published_at' => $this->is_published ? now() : null,
        ];

        // Handle featured image upload
        if ($this->featured_image) {
            $pageData['featured_image'] = $this->featured_image->store('pages', 'public');
        }

        Page::create($pageData);

        session()->flash('message', 'Page created successfully.');
        return redirect()->route('admin.pages.index');
    }

    public function render()
    {
        $templates = [
            'default' => 'Default Template',
            'homepage' => 'Homepage Template',
            'about' => 'About Page Template',
            'services' => 'Services Template',
            'contact' => 'Contact Template',
        ];

        return view('livewire.admin.pages.create', [
            'templates' => $templates,
        ])->layout('layouts.admin');
    }
}