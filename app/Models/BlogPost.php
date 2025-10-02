<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_data',
        'author_name',
        'author_email',
        'tags',
        'category',
        'is_published',
        'views_count',
        'likes_count',
        'published_at',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Get published posts
     */
    public static function getPublished()
    {
        return static::where('is_published', true)
                    ->orderBy('published_at', 'desc')
                    ->get();
    }

    /**
     * Get recent posts
     */
    public static function getRecent($limit = 5)
    {
        return static::where('is_published', true)
                    ->orderBy('published_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get posts by category
     */
    public static function getByCategory($category)
    {
        return static::where('category', $category)
                    ->where('is_published', true)
                    ->orderBy('published_at', 'desc')
                    ->get();
    }

    /**
     * Get posts by tag
     */
    public static function getByTag($tag)
    {
        return static::where('is_published', true)
                    ->whereJsonContains('tags', $tag)
                    ->orderBy('published_at', 'desc')
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
     * Get the post URL
     */
    public function getUrlAttribute()
    {
        return '/blog/' . $this->slug;
    }

    /**
     * Get the reading time
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        return $minutes . ' min read';
    }

    /**
     * Get the post title for SEO
     */
    public function getSeoTitleAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_title'] ?? $this->title;
    }

    /**
     * Get the post description for SEO
     */
    public function getSeoDescriptionAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_description'] ?? $this->excerpt;
    }

    /**
     * Get the post keywords for SEO
     */
    public function getSeoKeywordsAttribute()
    {
        $metaData = $this->meta_data ?? [];
        return $metaData['meta_keywords'] ?? '';
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Increment likes count
     */
    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    /**
     * Get blog categories
     */
    public static function getCategories()
    {
        return static::where('is_published', true)
                    ->whereNotNull('category')
                    ->distinct()
                    ->pluck('category')
                    ->sort()
                    ->values();
    }

    /**
     * Get all tags
     */
    public static function getAllTags()
    {
        $tags = static::where('is_published', true)
                     ->whereNotNull('tags')
                     ->pluck('tags')
                     ->flatten()
                     ->unique()
                     ->sort()
                     ->values();

        return $tags->toArray();
    }
}
