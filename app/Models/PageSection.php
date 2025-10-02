<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'section_type',
        'title',
        'content',
        'settings',
        'media',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'media' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the page that owns the section
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get active sections for a page
     */
    public static function getActiveForPage($pageId)
    {
        return static::where('page_id', $pageId)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get section types
     */
    public static function getSectionTypes()
    {
        return [
            'hero' => 'Hero Section',
            'services' => 'Services Section',
            'gallery' => 'Gallery Section',
            'testimonials' => 'Testimonials Section',
            'about' => 'About Section',
            'contact' => 'Contact Section',
            'team' => 'Team Section',
            'pricing' => 'Pricing Section',
            'faq' => 'FAQ Section',
            'blog' => 'Blog Section',
            'custom' => 'Custom Section',
        ];
    }

    /**
     * Get the section template
     */
    public function getTemplateAttribute()
    {
        return "sections.{$this->section_type}";
    }

    /**
     * Get the section data for rendering
     */
    public function getRenderData()
    {
        $data = [
            'section' => $this,
            'title' => $this->title,
            'content' => $this->content,
            'settings' => $this->settings ?? [],
            'media' => $this->media ?? [],
        ];

        // Add specific data based on section type
        switch ($this->section_type) {
            case 'services':
                $data['services'] = \App\Models\Service::where('is_active', true)->get();
                break;
            case 'gallery':
                $data['galleries'] = \App\Models\Gallery::where('is_active', true)->get();
                break;
            case 'testimonials':
                $data['testimonials'] = \App\Models\Testimonial::where('is_active', true)->get();
                break;
            case 'team':
                $data['staff'] = \App\Models\Staff::where('is_active', true)->get();
                break;
            case 'blog':
                $data['posts'] = \App\Models\BlogPost::where('is_published', true)
                    ->orderBy('published_at', 'desc')
                    ->limit(3)
                    ->get();
                break;
        }

        return $data;
    }
}
