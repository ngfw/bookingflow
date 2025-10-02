<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'images',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get gallery types
     */
    public static function getTypes()
    {
        return [
            'portfolio' => 'Portfolio',
            'before_after' => 'Before & After',
            'team' => 'Team Photos',
            'salon' => 'Salon Interior',
            'events' => 'Events',
            'awards' => 'Awards & Certifications',
        ];
    }

    /**
     * Get featured galleries
     */
    public static function getFeatured()
    {
        return static::where('is_featured', true)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get active galleries by type
     */
    public static function getByType($type)
    {
        return static::where('type', $type)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get the first image as thumbnail
     */
    public function getThumbnailAttribute()
    {
        $images = $this->images ?? [];
        return $images[0]['path'] ?? null;
    }

    /**
     * Get the image count
     */
    public function getImageCountAttribute()
    {
        return count($this->images ?? []);
    }

    /**
     * Add an image to the gallery
     */
    public function addImage($path, $alt = null, $caption = null)
    {
        $images = $this->images ?? [];
        $images[] = [
            'path' => $path,
            'alt' => $alt,
            'caption' => $caption,
            'uploaded_at' => now()->toISOString(),
        ];
        $this->images = $images;
        $this->save();
    }

    /**
     * Remove an image from the gallery
     */
    public function removeImage($index)
    {
        $images = $this->images ?? [];
        if (isset($images[$index])) {
            unset($images[$index]);
            $this->images = array_values($images);
            $this->save();
        }
    }
}
