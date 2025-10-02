<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\BlogPost;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\SalonSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ContentManager extends Component
{
    use WithFileUploads, WithPagination;

    public $activeTab = 'pages';
    public $showModal = false;
    public $modalType = '';
    public $editingItem = null;
    public $search = '';
    public $filter = 'all';

    // Page fields
    public $page_title = '';
    public $page_slug = '';
    public $page_excerpt = '';
    public $page_content = '';
    public $page_template = 'default';
    public $page_is_published = false;
    public $page_is_homepage = false;
    public $page_featured_image;

    // Blog post fields
    public $post_title = '';
    public $post_slug = '';
    public $post_excerpt = '';
    public $post_content = '';
    public $post_featured_image;
    public $post_author_name = '';
    public $post_author_email = '';
    public $post_tags = [];
    public $post_category = '';
    public $post_is_published = false;

    // Gallery fields
    public $gallery_name = '';
    public $gallery_description = '';
    public $gallery_type = 'portfolio';
    public $gallery_images = [];
    public $gallery_is_featured = false;
    public $gallery_is_active = true;

    // Testimonial fields
    public $testimonial_client_name = '';
    public $testimonial_client_email = '';
    public $testimonial_client_phone = '';
    public $testimonial_service_id = '';
    public $testimonial_rating = 5;
    public $testimonial_title = '';
    public $testimonial_content = '';
    public $testimonial_is_featured = false;
    public $testimonial_is_active = true;

    // Salon settings fields
    public $salon_name = '';
    public $salon_description = '';
    public $salon_logo;
    public $salon_favicon;
    public $salon_primary_color = '#ec4899';
    public $salon_secondary_color = '#8b5cf6';
    public $salon_accent_color = '#f59e0b';
    public $salon_font_family = 'Inter';
    public $salon_contact_info = [];
    public $salon_social_links = [];
    public $salon_seo_settings = [];
    public $salon_homepage_settings = [];

    protected $rules = [
        'page_title' => 'required|string|max:255',
        'page_slug' => 'required|string|max:255|unique:pages,slug',
        'post_title' => 'required|string|max:255',
        'post_slug' => 'required|string|max:255|unique:blog_posts,slug',
        'gallery_name' => 'required|string|max:255',
        'testimonial_client_name' => 'required|string|max:255',
        'testimonial_content' => 'required|string',
        'salon_name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadSalonSettings();
    }

    public function loadSalonSettings()
    {
        $settings = SalonSetting::getDefault();
        $this->salon_name = $settings->salon_name;
        $this->salon_description = $settings->salon_description;
        $this->salon_primary_color = $settings->primary_color;
        $this->salon_secondary_color = $settings->secondary_color;
        $this->salon_accent_color = $settings->accent_color;
        $this->salon_font_family = $settings->font_family;
        $this->salon_contact_info = $settings->contact_info ?? [];
        $this->salon_social_links = $settings->social_links ?? [];
        $this->salon_seo_settings = $settings->seo_settings ?? [];
        $this->salon_homepage_settings = $settings->homepage_settings ?? [];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->editingItem = $id;
        $this->resetForm();
        
        if ($id) {
            $this->loadItem($type, $id);
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
        $this->editingItem = null;
        $this->resetForm();
    }

    public function loadItem($type, $id)
    {
        switch ($type) {
            case 'page':
                $page = Page::findOrFail($id);
                $this->page_title = $page->title;
                $this->page_slug = $page->slug;
                $this->page_excerpt = $page->excerpt;
                $this->page_content = $page->content;
                $this->page_template = $page->template;
                $this->page_is_published = $page->is_published;
                $this->page_is_homepage = $page->is_homepage;
                break;
                
            case 'post':
                $post = BlogPost::findOrFail($id);
                $this->post_title = $post->title;
                $this->post_slug = $post->slug;
                $this->post_excerpt = $post->excerpt;
                $this->post_content = $post->content;
                $this->post_author_name = $post->author_name;
                $this->post_author_email = $post->author_email;
                $this->post_tags = $post->tags ?? [];
                $this->post_category = $post->category;
                $this->post_is_published = $post->is_published;
                break;
                
            case 'gallery':
                $gallery = Gallery::findOrFail($id);
                $this->gallery_name = $gallery->name;
                $this->gallery_description = $gallery->description;
                $this->gallery_type = $gallery->type;
                $this->gallery_images = $gallery->images ?? [];
                $this->gallery_is_featured = $gallery->is_featured;
                $this->gallery_is_active = $gallery->is_active;
                break;
                
            case 'testimonial':
                $testimonial = Testimonial::findOrFail($id);
                $this->testimonial_client_name = $testimonial->client_name;
                $this->testimonial_client_email = $testimonial->client_email;
                $this->testimonial_client_phone = $testimonial->client_phone;
                $this->testimonial_service_id = $testimonial->service_id;
                $this->testimonial_rating = $testimonial->rating;
                $this->testimonial_title = $testimonial->title;
                $this->testimonial_content = $testimonial->content;
                $this->testimonial_is_featured = $testimonial->is_featured;
                $this->testimonial_is_active = $testimonial->is_active;
                break;
        }
    }

    public function saveItem()
    {
        switch ($this->modalType) {
            case 'page':
                $this->savePage();
                break;
            case 'post':
                $this->savePost();
                break;
            case 'gallery':
                $this->saveGallery();
                break;
            case 'testimonial':
                $this->saveTestimonial();
                break;
            case 'settings':
                $this->saveSalonSettings();
                break;
        }
    }

    public function savePage()
    {
        $this->validate([
            'page_title' => 'required|string|max:255',
            'page_slug' => 'required|string|max:255',
        ]);

        $data = [
            'title' => $this->page_title,
            'slug' => $this->page_slug,
            'excerpt' => $this->page_excerpt,
            'content' => $this->page_content,
            'template' => $this->page_template,
            'is_published' => $this->page_is_published,
            'is_homepage' => $this->page_is_homepage,
        ];

        if ($this->page_featured_image) {
            $data['featured_image'] = $this->page_featured_image->store('pages', 'public');
        }

        if ($this->editingItem) {
            $page = Page::findOrFail($this->editingItem);
            $page->update($data);
        } else {
            Page::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Page saved successfully!');
    }

    public function savePost()
    {
        $this->validate([
            'post_title' => 'required|string|max:255',
            'post_slug' => 'required|string|max:255',
        ]);

        $data = [
            'title' => $this->post_title,
            'slug' => $this->post_slug,
            'excerpt' => $this->post_excerpt,
            'content' => $this->post_content,
            'author_name' => $this->post_author_name,
            'author_email' => $this->post_author_email,
            'tags' => $this->post_tags,
            'category' => $this->post_category,
            'is_published' => $this->post_is_published,
        ];

        if ($this->post_featured_image) {
            $data['featured_image'] = $this->post_featured_image->store('blog', 'public');
        }

        if ($this->post_is_published && !$this->editingItem) {
            $data['published_at'] = now();
        }

        if ($this->editingItem) {
            $post = BlogPost::findOrFail($this->editingItem);
            $post->update($data);
        } else {
            BlogPost::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Blog post saved successfully!');
    }

    public function saveGallery()
    {
        $this->validate([
            'gallery_name' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $this->gallery_name,
            'description' => $this->gallery_description,
            'type' => $this->gallery_type,
            'images' => $this->gallery_images,
            'is_featured' => $this->gallery_is_featured,
            'is_active' => $this->gallery_is_active,
        ];

        if ($this->editingItem) {
            $gallery = Gallery::findOrFail($this->editingItem);
            $gallery->update($data);
        } else {
            Gallery::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Gallery saved successfully!');
    }

    public function saveTestimonial()
    {
        $this->validate([
            'testimonial_client_name' => 'required|string|max:255',
            'testimonial_content' => 'required|string',
        ]);

        $data = [
            'client_name' => $this->testimonial_client_name,
            'client_email' => $this->testimonial_client_email,
            'client_phone' => $this->testimonial_client_phone,
            'service_id' => $this->testimonial_service_id,
            'rating' => $this->testimonial_rating,
            'title' => $this->testimonial_title,
            'content' => $this->testimonial_content,
            'is_featured' => $this->testimonial_is_featured,
            'is_active' => $this->testimonial_is_active,
        ];

        if ($this->editingItem) {
            $testimonial = Testimonial::findOrFail($this->editingItem);
            $testimonial->update($data);
        } else {
            Testimonial::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Testimonial saved successfully!');
    }

    public function saveSalonSettings()
    {
        $this->validate([
            'salon_name' => 'required|string|max:255',
        ]);

        $data = [
            'salon_name' => $this->salon_name,
            'salon_description' => $this->salon_description,
            'primary_color' => $this->salon_primary_color,
            'secondary_color' => $this->salon_secondary_color,
            'accent_color' => $this->salon_accent_color,
            'font_family' => $this->salon_font_family,
            'contact_info' => $this->salon_contact_info,
            'social_links' => $this->salon_social_links,
            'seo_settings' => $this->salon_seo_settings,
            'homepage_settings' => $this->salon_homepage_settings,
        ];

        if ($this->salon_logo) {
            $data['logo_path'] = $this->salon_logo->store('salon', 'public');
        }

        if ($this->salon_favicon) {
            $data['favicon_path'] = $this->salon_favicon->store('salon', 'public');
        }

        $settings = SalonSetting::getDefault();
        $settings->update($data);

        $this->closeModal();
        session()->flash('success', 'Salon settings saved successfully!');
    }

    public function deleteItem($type, $id)
    {
        switch ($type) {
            case 'page':
                Page::findOrFail($id)->delete();
                break;
            case 'post':
                BlogPost::findOrFail($id)->delete();
                break;
            case 'gallery':
                Gallery::findOrFail($id)->delete();
                break;
            case 'testimonial':
                Testimonial::findOrFail($id)->delete();
                break;
        }

        session()->flash('success', ucfirst($type) . ' deleted successfully!');
    }

    public function toggleStatus($type, $id, $field = 'is_active')
    {
        switch ($type) {
            case 'page':
                $item = Page::findOrFail($id);
                $item->update([$field => !$item->$field]);
                break;
            case 'post':
                $item = BlogPost::findOrFail($id);
                $item->update([$field => !$item->$field]);
                break;
            case 'gallery':
                $item = Gallery::findOrFail($id);
                $item->update([$field => !$item->$field]);
                break;
            case 'testimonial':
                $item = Testimonial::findOrFail($id);
                $item->update([$field => !$item->$field]);
                break;
        }

        session()->flash('success', ucfirst($type) . ' status updated!');
    }

    public function resetForm()
    {
        $this->page_title = '';
        $this->page_slug = '';
        $this->page_excerpt = '';
        $this->page_content = '';
        $this->page_template = 'default';
        $this->page_is_published = false;
        $this->page_is_homepage = false;
        $this->page_featured_image = null;

        $this->post_title = '';
        $this->post_slug = '';
        $this->post_excerpt = '';
        $this->post_content = '';
        $this->post_featured_image = null;
        $this->post_author_name = '';
        $this->post_author_email = '';
        $this->post_tags = [];
        $this->post_category = '';
        $this->post_is_published = false;

        $this->gallery_name = '';
        $this->gallery_description = '';
        $this->gallery_type = 'portfolio';
        $this->gallery_images = [];
        $this->gallery_is_featured = false;
        $this->gallery_is_active = true;

        $this->testimonial_client_name = '';
        $this->testimonial_client_email = '';
        $this->testimonial_client_phone = '';
        $this->testimonial_service_id = '';
        $this->testimonial_rating = 5;
        $this->testimonial_title = '';
        $this->testimonial_content = '';
        $this->testimonial_is_featured = false;
        $this->testimonial_is_active = true;
    }

    public function render()
    {
        $pages = Page::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->filter === 'draft') {
                $query->where('is_published', false);
            }
        })->orderBy('created_at', 'desc')->paginate(10);

        $posts = BlogPost::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->filter === 'draft') {
                $query->where('is_published', false);
            }
        })->orderBy('created_at', 'desc')->paginate(10);

        $galleries = Gallery::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($this->filter === 'active') {
                $query->where('is_active', true);
            }
        })->orderBy('created_at', 'desc')->paginate(10);

        $testimonials = Testimonial::when($this->search, function ($query) {
            $query->where('client_name', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($this->filter === 'active') {
                $query->where('is_active', true);
            }
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.content-manager', compact('pages', 'posts', 'galleries', 'testimonials'));
    }
}
