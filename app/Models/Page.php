<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'template',
        'meta_data',
        'page_settings',
        'featured_image',
        'is_published',
        'is_homepage',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'page_settings' => 'array',
        'is_published' => 'boolean',
        'is_homepage' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Get the page sections
     */
    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /**
     * Get the homepage
     */
    public static function getHomepage()
    {
        return static::where('is_homepage', true)
                    ->where('is_published', true)
                    ->first();
    }

    /**
     * Get published pages
     */
    public static function getPublished()
    {
        return static::where('is_published', true)
                    ->where('is_homepage', false)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get the route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the page URL
     */
    public function getUrlAttribute()
    {
        if ($this->is_homepage) {
            return '/';
        }
        return '/' . $this->slug;
    }

    /**
     * Get the page title for SEO
     */
    public function getSeoTitleAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_title'] ?? $this->title;
    }

    /**
     * Get the page description for SEO
     */
    public function getSeoDescriptionAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_description'] ?? $this->excerpt;
    }

    /**
     * Get the page keywords for SEO
     */
    public function getSeoKeywordsAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_keywords'] ?? '';
    }
}
