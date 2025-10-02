<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SalonSetting;
use App\Services\SEOService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    protected $seoService;
    protected $analyticsService;

    public function __construct(SEOService $seoService, AnalyticsService $analyticsService)
    {
        $this->seoService = $seoService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the homepage
     */
    public function homepage(Request $request)
    {
        // Track page view
        $this->analyticsService->trackPageView($request, 'Homepage');

        $homepage = Page::getHomepage();
        $settings = SalonSetting::getDefault();

        if (!$homepage) {
            // Fallback to default homepage content
            return $this->defaultHomepage($request, $settings);
        }

        $seoData = $this->seoService->generateMetaTags($homepage);
        $structuredData = $this->seoService->generateStructuredData($homepage);

        return view('pages.dynamic', compact('homepage', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display a dynamic page
     */
    public function show(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)
                   ->where('is_published', true)
                   ->firstOrFail();

        // Track page view
        $this->analyticsService->trackPageView($request, $page->title);

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags($page);
        $structuredData = $this->seoService->generateStructuredData($page);

        return view('pages.dynamic', compact('page', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display blog posts
     */
    public function blog(Request $request)
    {
        // Track page view
        $this->analyticsService->trackPageView($request, 'Blog');

        $posts = \App\Models\BlogPost::where('is_published', true)
                                   ->orderBy('published_at', 'desc')
                                   ->paginate(10);

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags(null, 'website');
        $structuredData = $this->seoService->generateStructuredData(null, 'website');

        return view('pages.blog', compact('posts', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display a single blog post
     */
    public function blogPost(Request $request, $slug)
    {
        $post = \App\Models\BlogPost::where('slug', $slug)
                                   ->where('is_published', true)
                                   ->firstOrFail();

        // Track page view
        $this->analyticsService->trackPageView($request, $post->title);

        // Increment views
        $post->incrementViews();

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags($post, 'post');
        $structuredData = $this->seoService->generateStructuredData($post, 'post');

        return view('pages.blog-post', compact('post', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display gallery
     */
    public function gallery(Request $request)
    {
        // Track page view
        $this->analyticsService->trackPageView($request, 'Gallery');

        $galleries = \App\Models\Gallery::where('is_active', true)
                                      ->orderBy('sort_order')
                                      ->get();

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags(null, 'website');
        $structuredData = $this->seoService->generateStructuredData(null, 'website');

        return view('pages.gallery', compact('galleries', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display services
     */
    public function services(Request $request)
    {
        // Track page view
        $this->analyticsService->trackPageView($request, 'Services');

        $services = \App\Models\Service::where('is_active', true)
                                     ->orderBy('sort_order')
                                     ->get();

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags(null, 'website');
        $structuredData = $this->seoService->generateStructuredData(null, 'website');

        return view('pages.services', compact('services', 'settings', 'seoData', 'structuredData'));
    }

    /**
     * Display contact page
     */
    public function contact(Request $request)
    {
        // Track page view
        $this->analyticsService->trackPageView($request, 'Contact');

        $settings = SalonSetting::getDefault();
        $seoData = $this->seoService->generateMetaTags(null, 'website');
        $structuredData = $this->seoService->generateStructuredData(null, 'website');

        return view('pages.contact', compact('settings', 'seoData', 'structuredData'));
    }

    /**
     * Default homepage fallback
     */
    private function defaultHomepage(Request $request, $settings)
    {
        $seoData = $this->seoService->generateMetaTags(null, 'website');
        $structuredData = $this->seoService->generateStructuredData(null, 'website');

        return view('pages.default-homepage', compact('settings', 'seoData', 'structuredData'));
    }
}
