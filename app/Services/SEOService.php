<?php

namespace App\Services;

use App\Models\Page;
use App\Models\BlogPost;
use App\Models\SalonSetting;
use Illuminate\Support\Str;

class SEOService
{
    /**
     * Generate SEO meta tags for a page
     */
    public function generateMetaTags($page = null, $type = 'page')
    {
        $settings = SalonSetting::getDefault();
        $seoData = $settings->seo_settings ?? [];
        
        $defaultTitle = $seoData['meta_title'] ?? $settings->salon_name;
        $defaultDescription = $seoData['meta_description'] ?? $settings->salon_description;
        $defaultKeywords = $seoData['meta_keywords'] ?? '';

        if ($page) {
            $title = $page->seo_title ?: $page->title;
            $description = $page->seo_description ?: $page->excerpt ?: $defaultDescription;
            $keywords = $page->seo_keywords ?: $defaultKeywords;
        } else {
            $title = $defaultTitle;
            $description = $defaultDescription;
            $keywords = $defaultKeywords;
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $page->featured_image ?? $settings->logo_path,
            'og_url' => request()->url(),
            'og_type' => $type === 'post' ? 'article' : 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => $page->featured_image ?? $settings->logo_path,
        ];
    }

    /**
     * Generate structured data for a page
     */
    public function generateStructuredData($page = null, $type = 'page')
    {
        $settings = SalonSetting::getDefault();
        $contactInfo = $settings->contact_info ?? [];

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => $type === 'post' ? 'Article' : 'WebPage',
            'name' => $page ? $page->title : $settings->salon_name,
            'description' => $page ? $page->excerpt : $settings->salon_description,
            'url' => request()->url(),
        ];

        if ($type === 'post' && $page) {
            $structuredData['@type'] = 'Article';
            $structuredData['headline'] = $page->title;
            $structuredData['datePublished'] = $page->published_at?->toISOString();
            $structuredData['dateModified'] = $page->updated_at->toISOString();
            $structuredData['author'] = [
                '@type' => 'Person',
                'name' => $page->author_name ?: 'Salon Staff',
            ];
            if ($page->featured_image) {
                $structuredData['image'] = asset('storage/' . $page->featured_image);
            }
        }

        // Add organization data
        $structuredData['publisher'] = [
            '@type' => 'Organization',
            'name' => $settings->salon_name,
            'description' => $settings->salon_description,
            'url' => config('app.url'),
        ];

        if ($settings->logo_path) {
            $structuredData['publisher']['logo'] = [
                '@type' => 'ImageObject',
                'url' => asset('storage/' . $settings->logo_path),
            ];
        }

        if (!empty($contactInfo)) {
            $structuredData['publisher']['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $contactInfo['phone'] ?? '',
                'email' => $contactInfo['email'] ?? '',
                'contactType' => 'customer service',
            ];

            if (isset($contactInfo['address'])) {
                $structuredData['publisher']['address'] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $contactInfo['address'],
                ];
            }
        }

        return $structuredData;
    }

    /**
     * Generate sitemap data
     */
    public function generateSitemapData()
    {
        $sitemap = [];
        
        // Add homepage
        $sitemap[] = [
            'url' => config('app.url'),
            'lastmod' => now()->toISOString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Add published pages
        $pages = Page::where('is_published', true)->get();
        foreach ($pages as $page) {
            $sitemap[] = [
                'url' => config('app.url') . $page->url,
                'lastmod' => $page->updated_at->toISOString(),
                'changefreq' => 'weekly',
                'priority' => $page->is_homepage ? '1.0' : '0.8',
            ];
        }

        // Add published blog posts
        $posts = BlogPost::where('is_published', true)->get();
        foreach ($posts as $post) {
            $sitemap[] = [
                'url' => config('app.url') . $post->url,
                'lastmod' => $post->updated_at->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        return $sitemap;
    }

    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "Sitemap: " . config('app.url') . "/sitemap.xml\n";
        
        return $content;
    }

    /**
     * Analyze SEO score for content
     */
    public function analyzeSEOScore($content, $title, $description, $keywords)
    {
        $score = 0;
        $maxScore = 100;
        $issues = [];

        // Title analysis
        if (empty($title)) {
            $issues[] = 'Missing title';
        } else {
            $score += 10;
            if (strlen($title) < 30) {
                $issues[] = 'Title too short (recommended: 30-60 characters)';
            } elseif (strlen($title) > 60) {
                $issues[] = 'Title too long (recommended: 30-60 characters)';
            } else {
                $score += 10;
            }
        }

        // Description analysis
        if (empty($description)) {
            $issues[] = 'Missing meta description';
        } else {
            $score += 10;
            if (strlen($description) < 120) {
                $issues[] = 'Description too short (recommended: 120-160 characters)';
            } elseif (strlen($description) > 160) {
                $issues[] = 'Description too long (recommended: 120-160 characters)';
            } else {
                $score += 10;
            }
        }

        // Keywords analysis
        if (empty($keywords)) {
            $issues[] = 'Missing keywords';
        } else {
            $score += 5;
        }

        // Content analysis
        if (empty($content)) {
            $issues[] = 'No content';
        } else {
            $score += 10;
            $wordCount = str_word_count(strip_tags($content));
            if ($wordCount < 300) {
                $issues[] = 'Content too short (recommended: 300+ words)';
            } else {
                $score += 10;
            }

            // Check for headings
            if (preg_match('/<h[1-6]/', $content)) {
                $score += 5;
            } else {
                $issues[] = 'No headings found';
            }

            // Check for images
            if (preg_match('/<img/', $content)) {
                $score += 5;
            } else {
                $issues[] = 'No images found';
            }

            // Check for internal links
            if (preg_match('/<a[^>]*href=["\'][^"\']*["\']/', $content)) {
                $score += 5;
            } else {
                $issues[] = 'No internal links found';
            }
        }

        // URL structure
        $url = request()->url();
        if (strlen($url) > 100) {
            $issues[] = 'URL too long';
        } else {
            $score += 5;
        }

        return [
            'score' => min($score, $maxScore),
            'max_score' => $maxScore,
            'issues' => $issues,
            'grade' => $this->getSEOGrade($score),
        ];
    }

    /**
     * Get SEO grade based on score
     */
    private function getSEOGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Generate meta tags HTML
     */
    public function generateMetaTagsHTML($metaData)
    {
        $html = '';
        
        // Basic meta tags
        $html .= '<title>' . e($metaData['title']) . '</title>' . "\n";
        $html .= '<meta name="description" content="' . e($metaData['description']) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . e($metaData['keywords']) . '">' . "\n";
        
        // Open Graph tags
        $html .= '<meta property="og:title" content="' . e($metaData['og_title']) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($metaData['og_description']) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . e($metaData['og_image']) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($metaData['og_url']) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($metaData['og_type']) . '">' . "\n";
        
        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="' . e($metaData['twitter_card']) . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . e($metaData['twitter_title']) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($metaData['twitter_description']) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . e($metaData['twitter_image']) . '">' . "\n";
        
        return $html;
    }

    /**
     * Generate structured data JSON-LD
     */
    public function generateStructuredDataJSON($structuredData)
    {
        return '<script type="application/ld+json">' . json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
