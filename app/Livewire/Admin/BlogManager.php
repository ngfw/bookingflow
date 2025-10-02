<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class BlogManager extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $editingPost = null;
    public $search = '';
    public $filter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Blog post form fields
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
    public $new_tag = '';

    // Content editor
    public $editorContent = '';
    public $showPreview = false;

    protected $rules = [
        'post_title' => 'required|string|max:255',
        'post_slug' => 'required|string|max:255',
        'post_excerpt' => 'nullable|string',
        'post_content' => 'required|string',
        'post_author_name' => 'nullable|string|max:255',
        'post_author_email' => 'nullable|email|max:255',
        'post_category' => 'nullable|string|max:255',
        'post_is_published' => 'boolean',
    ];

    public function mount()
    {
        // Initialize with default values
    }

    public function openModal($postId = null)
    {
        $this->editingPost = $postId;
        $this->resetForm();
        
        if ($postId) {
            $this->loadPost($postId);
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPost = null;
        $this->resetForm();
    }

    public function loadPost($postId)
    {
        $post = BlogPost::findOrFail($postId);
        $this->post_title = $post->title;
        $this->post_slug = $post->slug;
        $this->post_excerpt = $post->excerpt;
        $this->post_content = $post->content;
        $this->post_author_name = $post->author_name;
        $this->post_author_email = $post->author_email;
        $this->post_tags = $post->tags ?? [];
        $this->post_category = $post->category;
        $this->post_is_published = $post->is_published;
        $this->editorContent = $post->content;
    }

    public function savePost()
    {
        $this->validate();

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

        if ($this->post_is_published && !$this->editingPost) {
            $data['published_at'] = now();
        }

        if ($this->editingPost) {
            $post = BlogPost::findOrFail($this->editingPost);
            $post->update($data);
        } else {
            BlogPost::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Blog post saved successfully!');
    }

    public function deletePost($postId)
    {
        BlogPost::findOrFail($postId)->delete();
        session()->flash('success', 'Blog post deleted successfully!');
    }

    public function toggleStatus($postId, $field = 'is_published')
    {
        $post = BlogPost::findOrFail($postId);
        $post->update([$field => !$post->$field]);
        session()->flash('success', 'Blog post status updated!');
    }

    public function duplicatePost($postId)
    {
        $post = BlogPost::findOrFail($postId);
        $newPost = $post->replicate();
        $newPost->title = $post->title . ' (Copy)';
        $newPost->slug = $post->slug . '-copy';
        $newPost->is_published = false;
        $newPost->published_at = null;
        $newPost->save();

        session()->flash('success', 'Blog post duplicated successfully!');
    }

    public function addTag()
    {
        if (!empty($this->new_tag) && !in_array($this->new_tag, $this->post_tags)) {
            $this->post_tags[] = $this->new_tag;
            $this->new_tag = '';
        }
    }

    public function removeTag($index)
    {
        if (isset($this->post_tags[$index])) {
            unset($this->post_tags[$index]);
            $this->post_tags = array_values($this->post_tags);
        }
    }

    public function generateSlug()
    {
        if (!empty($this->post_title)) {
            $this->post_slug = Str::slug($this->post_title);
        }
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function resetForm()
    {
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
        $this->new_tag = '';
        $this->editorContent = '';
        $this->showPreview = false;
    }

    public function getCategories()
    {
        return BlogPost::getCategories();
    }

    public function getAllTags()
    {
        return BlogPost::getAllTags();
    }

    public function render()
    {
        $posts = BlogPost::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->filter === 'draft') {
                $query->where('is_published', false);
            } elseif ($this->filter === 'featured') {
                $query->where('is_featured', true);
            } else {
                $query->where('category', $this->filter);
            }
        })->orderBy($this->sortBy, $this->sortDirection)->paginate(12);

        return view('livewire.admin.blog-manager', [
            'posts' => $posts,
            'categories' => $this->getCategories(),
            'allTags' => $this->getAllTags(),
        ]);
    }
}
