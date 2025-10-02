<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Models\BlogPost;
use App\Models\SalonSetting;
use App\Services\SEOService;
use Livewire\Component;
use Livewire\WithPagination;

class SEOManager extends Component
{
    use WithPagination;

    public $activeTab = 'overview';
    public $showModal = false;
    public $editingItem = null;
    public $search = '';
    public $filter = 'all';

    // SEO fields
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $og_title = '';
    public $og_description = '';
    public $og_image = '';
    public $twitter_title = '';
    public $twitter_description = '';
    public $twitter_image = '';

    // Analysis fields
    public $seo_score = 0;
    public $seo_grade = 'F';
    public $seo_issues = [];

    protected $seoService;

    public function mount()
    {
        $this->seoService = new SEOService();
        $this->loadSalonSEOSettings();
    }

    public function loadSalonSEOSettings()
    {
        $settings = SalonSetting::getDefault();
        $seoData = $settings->seo_settings ?? [];
        
        $this->meta_title = $seoData['meta_title'] ?? $settings->salon_name;
        $this->meta_description = $seoData['meta_description'] ?? $settings->salon_description;
        $this->meta_keywords = $seoData['meta_keywords'] ?? '';
        $this->og_title = $seoData['og_title'] ?? $this->meta_title;
        $this->og_description = $seoData['og_description'] ?? $this->meta_description;
        $this->og_image = $seoData['og_image'] ?? $settings->logo_path;
        $this->twitter_title = $seoData['twitter_title'] ?? $this->meta_title;
        $this->twitter_description = $seoData['twitter_description'] ?? $this->meta_description;
        $this->twitter_image = $seoData['twitter_image'] ?? $settings->logo_path;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openModal($type, $id = null)
    {
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
        $this->editingItem = null;
        $this->resetForm();
    }

    public function loadItem($type, $id)
    {
        switch ($type) {
            case 'page':
                $page = Page::findOrFail($id);
                $metaData = $page->meta_data ?? [];
                $this->meta_title = $metaData['meta_title'] ?? $page->title;
                $this->meta_description = $metaData['meta_description'] ?? $page->excerpt;
                $this->meta_keywords = $metaData['meta_keywords'] ?? '';
                $this->og_title = $metaData['og_title'] ?? $this->meta_title;
                $this->og_description = $metaData['og_description'] ?? $this->meta_description;
                $this->og_image = $metaData['og_image'] ?? $page->featured_image;
                $this->twitter_title = $metaData['twitter_title'] ?? $this->meta_title;
                $this->twitter_description = $metaData['twitter_description'] ?? $this->meta_description;
                $this->twitter_image = $metaData['twitter_image'] ?? $page->featured_image;
                break;
                
            case 'post':
                $post = BlogPost::findOrFail($id);
                $metaData = $post->meta_data ?? [];
                $this->meta_title = $metaData['meta_title'] ?? $post->title;
                $this->meta_description = $metaData['meta_description'] ?? $post->excerpt;
                $this->meta_keywords = $metaData['meta_keywords'] ?? '';
                $this->og_title = $metaData['og_title'] ?? $this->meta_title;
                $this->og_description = $metaData['og_description'] ?? $this->meta_description;
                $this->og_image = $metaData['og_image'] ?? $post->featured_image;
                $this->twitter_title = $metaData['twitter_title'] ?? $this->meta_title;
                $this->twitter_description = $metaData['twitter_description'] ?? $this->meta_description;
                $this->twitter_image = $metaData['twitter_image'] ?? $post->featured_image;
                break;
        }
    }

    public function saveSEOSettings()
    {
        $this->validate([
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
        ]);

        $seoData = [
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'twitter_title' => $this->twitter_title,
            'twitter_description' => $this->twitter_description,
            'twitter_image' => $this->twitter_image,
        ];

        $settings = SalonSetting::getDefault();
        $currentSettings = $settings->seo_settings ?? [];
        $updatedSettings = array_merge($currentSettings, $seoData);
        
        $settings->update(['seo_settings' => $updatedSettings]);

        $this->closeModal();
        session()->flash('success', 'SEO settings saved successfully!');
    }

    public function saveItemSEO()
    {
        $this->validate([
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
        ]);

        $metaData = [
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'twitter_title' => $this->twitter_title,
            'twitter_description' => $this->twitter_description,
            'twitter_image' => $this->twitter_image,
        ];

        // Determine item type and ID from the editing context
        // This would need to be passed from the calling component
        // For now, we'll assume it's a page
        if ($this->editingItem) {
            $page = Page::findOrFail($this->editingItem);
            $page->update(['meta_data' => $metaData]);
        }

        $this->closeModal();
        session()->flash('success', 'SEO data saved successfully!');
    }

    public function analyzeSEO($type, $id)
    {
        switch ($type) {
            case 'page':
                $page = Page::findOrFail($id);
                $analysis = $this->seoService->analyzeSEOScore(
                    $page->content,
                    $page->seo_title,
                    $page->seo_description,
                    $page->seo_keywords
                );
                break;
                
            case 'post':
                $post = BlogPost::findOrFail($id);
                $analysis = $this->seoService->analyzeSEOScore(
                    $post->content,
                    $post->seo_title,
                    $post->seo_description,
                    $post->seo_keywords
                );
                break;
                
            default:
                $analysis = ['score' => 0, 'grade' => 'F', 'issues' => []];
        }

        $this->seo_score = $analysis['score'];
        $this->seo_grade = $analysis['grade'];
        $this->seo_issues = $analysis['issues'];
    }

    public function generateSitemap()
    {
        $sitemapData = $this->seoService->generateSitemapData();
        
        // Generate XML sitemap
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($sitemapData as $item) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . e($item['url']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $item['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $item['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $item['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        // Save sitemap to public directory
        file_put_contents(public_path('sitemap.xml'), $xml);
        
        session()->flash('success', 'Sitemap generated successfully!');
    }

    public function generateRobotsTxt()
    {
        $robotsContent = $this->seoService->generateRobotsTxt();
        
        // Save robots.txt to public directory
        file_put_contents(public_path('robots.txt'), $robotsContent);
        
        session()->flash('success', 'Robots.txt generated successfully!');
    }

    public function resetForm()
    {
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->og_title = '';
        $this->og_description = '';
        $this->og_image = '';
        $this->twitter_title = '';
        $this->twitter_description = '';
        $this->twitter_image = '';
        $this->seo_score = 0;
        $this->seo_grade = 'F';
        $this->seo_issues = [];
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

        return view('livewire.admin.seo-manager', compact('pages', 'posts'));
    }
}
