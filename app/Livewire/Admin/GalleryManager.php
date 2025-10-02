<?php

namespace App\Livewire\Admin;

use App\Models\Gallery;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class GalleryManager extends Component
{
    use WithFileUploads, WithPagination;

    public $showModal = false;
    public $editingGallery = null;
    public $search = '';
    public $filter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Gallery form fields
    public $gallery_name = '';
    public $gallery_description = '';
    public $gallery_type = 'portfolio';
    public $gallery_images = [];
    public $gallery_is_featured = false;
    public $gallery_is_active = true;
    public $uploaded_images = [];

    // Image management
    public $selectedImages = [];
    public $showImageModal = false;
    public $editingImage = null;
    public $image_alt = '';
    public $image_caption = '';

    protected $rules = [
        'gallery_name' => 'required|string|max:255',
        'gallery_description' => 'nullable|string',
        'gallery_type' => 'required|string',
        'gallery_is_featured' => 'boolean',
        'gallery_is_active' => 'boolean',
    ];

    public function mount()
    {
        // Initialize with default values
    }

    public function openModal($galleryId = null)
    {
        $this->editingGallery = $galleryId;
        $this->resetForm();
        
        if ($galleryId) {
            $this->loadGallery($galleryId);
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingGallery = null;
        $this->resetForm();
    }

    public function loadGallery($galleryId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $this->gallery_name = $gallery->name;
        $this->gallery_description = $gallery->description;
        $this->gallery_type = $gallery->type;
        $this->gallery_images = $gallery->images ?? [];
        $this->gallery_is_featured = $gallery->is_featured;
        $this->gallery_is_active = $gallery->is_active;
    }

    public function saveGallery()
    {
        $this->validate();

        $data = [
            'name' => $this->gallery_name,
            'description' => $this->gallery_description,
            'type' => $this->gallery_type,
            'images' => $this->gallery_images,
            'is_featured' => $this->gallery_is_featured,
            'is_active' => $this->gallery_is_active,
        ];

        if ($this->editingGallery) {
            $gallery = Gallery::findOrFail($this->editingGallery);
            $gallery->update($data);
        } else {
            Gallery::create($data);
        }

        $this->closeModal();
        session()->flash('success', 'Gallery saved successfully!');
    }

    public function deleteGallery($galleryId)
    {
        Gallery::findOrFail($galleryId)->delete();
        session()->flash('success', 'Gallery deleted successfully!');
    }

    public function toggleStatus($galleryId, $field = 'is_active')
    {
        $gallery = Gallery::findOrFail($galleryId);
        $gallery->update([$field => !$gallery->$field]);
        session()->flash('success', 'Gallery status updated!');
    }

    public function duplicateGallery($galleryId)
    {
        $gallery = Gallery::findOrFail($galleryId);
        $newGallery = $gallery->replicate();
        $newGallery->name = $gallery->name . ' (Copy)';
        $newGallery->save();

        session()->flash('success', 'Gallery duplicated successfully!');
    }

    public function uploadImages()
    {
        $this->validate([
            'uploaded_images.*' => 'image|max:5120', // 5MB max per image
        ]);

        $uploadedPaths = [];
        foreach ($this->uploaded_images as $image) {
            $path = $image->store('galleries', 'public');
            $uploadedPaths[] = [
                'path' => $path,
                'alt' => '',
                'caption' => '',
                'uploaded_at' => now()->toISOString(),
            ];
        }

        $this->gallery_images = array_merge($this->gallery_images, $uploadedPaths);
        $this->uploaded_images = [];
        
        session()->flash('success', count($uploadedPaths) . ' images uploaded successfully!');
    }

    public function removeImage($index)
    {
        if (isset($this->gallery_images[$index])) {
            unset($this->gallery_images[$index]);
            $this->gallery_images = array_values($this->gallery_images);
        }
    }

    public function editImage($index)
    {
        $this->editingImage = $index;
        $image = $this->gallery_images[$index] ?? [];
        $this->image_alt = $image['alt'] ?? '';
        $this->image_caption = $image['caption'] ?? '';
        $this->showImageModal = true;
    }

    public function saveImageDetails()
    {
        if ($this->editingImage !== null && isset($this->gallery_images[$this->editingImage])) {
            $this->gallery_images[$this->editingImage]['alt'] = $this->image_alt;
            $this->gallery_images[$this->editingImage]['caption'] = $this->image_caption;
        }
        
        $this->closeImageModal();
    }

    public function closeImageModal()
    {
        $this->showImageModal = false;
        $this->editingImage = null;
        $this->image_alt = '';
        $this->image_caption = '';
    }

    public function reorderImages($orderedIndexes)
    {
        $reorderedImages = [];
        foreach ($orderedIndexes as $index) {
            if (isset($this->gallery_images[$index])) {
                $reorderedImages[] = $this->gallery_images[$index];
            }
        }
        $this->gallery_images = $reorderedImages;
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

    public function resetForm()
    {
        $this->gallery_name = '';
        $this->gallery_description = '';
        $this->gallery_type = 'portfolio';
        $this->gallery_images = [];
        $this->gallery_is_featured = false;
        $this->gallery_is_active = true;
        $this->uploaded_images = [];
        $this->selectedImages = [];
    }

    public function getGalleryTypes()
    {
        return Gallery::getTypes();
    }

    public function getFeaturedGalleries()
    {
        return Gallery::getFeatured();
    }

    public function getGalleriesByType($type)
    {
        return Gallery::getByType($type);
    }

    public function render()
    {
        $galleries = Gallery::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        })->when($this->filter !== 'all', function ($query) {
            if ($this->filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($this->filter === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filter === 'inactive') {
                $query->where('is_active', false);
            } else {
                $query->where('type', $this->filter);
            }
        })->orderBy($this->sortBy, $this->sortDirection)->paginate(12);

        return view('livewire.admin.gallery-manager', [
            'galleries' => $galleries,
            'galleryTypes' => $this->getGalleryTypes(),
        ]);
    }
}
