<?php

namespace App\Services;

use App\Models\SalonSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * Get social media links from salon settings
     */
    public function getSocialLinks()
    {
        $settings = SalonSetting::getDefault();
        return $settings->social_links ?? [];
    }

    /**
     * Update social media links
     */
    public function updateSocialLinks($links)
    {
        $settings = SalonSetting::getDefault();
        $currentSettings = $settings->social_links ?? [];
        $updatedSettings = array_merge($currentSettings, $links);
        
        $settings->update(['social_links' => $updatedSettings]);
        return $updatedSettings;
    }

    /**
     * Generate social sharing URLs
     */
    public function generateSharingUrls($url, $title, $description = '', $image = '')
    {
        $encodedUrl = urlencode($url);
        $encodedTitle = urlencode($title);
        $encodedDescription = urlencode($description);
        $encodedImage = urlencode($image);

        return [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'twitter' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'pinterest' => "https://pinterest.com/pin/create/button/?url={$encodedUrl}&media={$encodedImage}&description={$encodedDescription}",
            'whatsapp' => "https://wa.me/?text={$encodedTitle}%20{$encodedUrl}",
            'telegram' => "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}",
            'email' => "mailto:?subject={$encodedTitle}&body={$encodedDescription}%20{$encodedUrl}",
        ];
    }

    /**
     * Get Facebook page insights (requires Facebook API)
     */
    public function getFacebookInsights($pageId, $accessToken)
    {
        try {
            $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/insights", [
                'metric' => 'page_fans,page_impressions,page_engaged_users',
                'period' => 'day',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Facebook insights error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get Instagram insights (requires Instagram Business Account)
     */
    public function getInstagramInsights($accountId, $accessToken)
    {
        try {
            $response = Http::get("https://graph.facebook.com/v18.0/{$accountId}/insights", [
                'metric' => 'impressions,reach,profile_views',
                'period' => 'day',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Instagram insights error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Post to Facebook page (requires Facebook API)
     */
    public function postToFacebook($pageId, $accessToken, $message, $link = null, $image = null)
    {
        try {
            $data = [
                'message' => $message,
                'access_token' => $accessToken,
            ];

            if ($link) {
                $data['link'] = $link;
            }

            if ($image) {
                $data['url'] = $image;
            }

            $response = Http::post("https://graph.facebook.com/v18.0/{$pageId}/feed", $data);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Facebook post error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Post to Instagram (requires Instagram Business Account)
     */
    public function postToInstagram($accountId, $accessToken, $imageUrl, $caption)
    {
        try {
            // First, create a media container
            $mediaResponse = Http::post("https://graph.facebook.com/v18.0/{$accountId}/media", [
                'image_url' => $imageUrl,
                'caption' => $caption,
                'access_token' => $accessToken,
            ]);

            if ($mediaResponse->successful()) {
                $mediaId = $mediaResponse->json()['id'];

                // Then publish the media
                $publishResponse = Http::post("https://graph.facebook.com/v18.0/{$accountId}/media_publish", [
                    'creation_id' => $mediaId,
                    'access_token' => $accessToken,
                ]);

                if ($publishResponse->successful()) {
                    return $publishResponse->json();
                }
            }
        } catch (\Exception $e) {
            Log::error('Instagram post error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get social media analytics summary
     */
    public function getAnalyticsSummary()
    {
        $socialLinks = $this->getSocialLinks();
        $summary = [];

        foreach ($socialLinks as $platform => $url) {
            if (!empty($url)) {
                $summary[$platform] = [
                    'url' => $url,
                    'followers' => $this->getFollowerCount($platform, $url),
                    'engagement' => $this->getEngagementRate($platform, $url),
                ];
            }
        }

        return $summary;
    }

    /**
     * Get follower count for a platform (mock implementation)
     */
    private function getFollowerCount($platform, $url)
    {
        // This would typically involve API calls to each platform
        // For now, return mock data
        $mockCounts = [
            'facebook' => rand(1000, 10000),
            'instagram' => rand(500, 5000),
            'twitter' => rand(200, 2000),
            'youtube' => rand(100, 1000),
        ];

        return $mockCounts[$platform] ?? 0;
    }

    /**
     * Get engagement rate for a platform (mock implementation)
     */
    private function getEngagementRate($platform, $url)
    {
        // This would typically involve API calls to each platform
        // For now, return mock data
        $mockRates = [
            'facebook' => rand(2, 8) / 100,
            'instagram' => rand(3, 12) / 100,
            'twitter' => rand(1, 5) / 100,
            'youtube' => rand(1, 4) / 100,
        ];

        return $mockRates[$platform] ?? 0;
    }

    /**
     * Generate social media sharing buttons HTML
     */
    public function generateSharingButtons($url, $title, $description = '', $image = '')
    {
        $sharingUrls = $this->generateSharingUrls($url, $title, $description, $image);

        $buttons = [
            'facebook' => [
                'url' => $sharingUrls['facebook'],
                'icon' => 'fab fa-facebook-f',
                'color' => 'bg-blue-600 hover:bg-blue-700',
                'label' => 'Share on Facebook',
            ],
            'twitter' => [
                'url' => $sharingUrls['twitter'],
                'icon' => 'fab fa-twitter',
                'color' => 'bg-blue-400 hover:bg-blue-500',
                'label' => 'Share on Twitter',
            ],
            'linkedin' => [
                'url' => $sharingUrls['linkedin'],
                'icon' => 'fab fa-linkedin-in',
                'color' => 'bg-blue-700 hover:bg-blue-800',
                'label' => 'Share on LinkedIn',
            ],
            'pinterest' => [
                'url' => $sharingUrls['pinterest'],
                'icon' => 'fab fa-pinterest',
                'color' => 'bg-red-600 hover:bg-red-700',
                'label' => 'Share on Pinterest',
            ],
            'whatsapp' => [
                'url' => $sharingUrls['whatsapp'],
                'icon' => 'fab fa-whatsapp',
                'color' => 'bg-green-500 hover:bg-green-600',
                'label' => 'Share on WhatsApp',
            ],
            'telegram' => [
                'url' => $sharingUrls['telegram'],
                'icon' => 'fab fa-telegram',
                'color' => 'bg-blue-500 hover:bg-blue-600',
                'label' => 'Share on Telegram',
            ],
            'email' => [
                'url' => $sharingUrls['email'],
                'icon' => 'fas fa-envelope',
                'color' => 'bg-gray-600 hover:bg-gray-700',
                'label' => 'Share via Email',
            ],
        ];

        return $buttons;
    }

    /**
     * Generate social media follow buttons HTML
     */
    public function generateFollowButtons()
    {
        $socialLinks = $this->getSocialLinks();
        $buttons = [];

        $platforms = [
            'facebook' => [
                'icon' => 'fab fa-facebook-f',
                'color' => 'bg-blue-600 hover:bg-blue-700',
                'label' => 'Follow on Facebook',
            ],
            'instagram' => [
                'icon' => 'fab fa-instagram',
                'color' => 'bg-pink-600 hover:bg-pink-700',
                'label' => 'Follow on Instagram',
            ],
            'twitter' => [
                'icon' => 'fab fa-twitter',
                'color' => 'bg-blue-400 hover:bg-blue-500',
                'label' => 'Follow on Twitter',
            ],
            'youtube' => [
                'icon' => 'fab fa-youtube',
                'color' => 'bg-red-600 hover:bg-red-700',
                'label' => 'Subscribe on YouTube',
            ],
            'tiktok' => [
                'icon' => 'fab fa-tiktok',
                'color' => 'bg-black hover:bg-gray-800',
                'label' => 'Follow on TikTok',
            ],
        ];

        foreach ($platforms as $platform => $config) {
            if (!empty($socialLinks[$platform])) {
                $buttons[$platform] = array_merge($config, [
                    'url' => $socialLinks[$platform],
                ]);
            }
        }

        return $buttons;
    }

    /**
     * Schedule social media posts
     */
    public function schedulePost($platform, $content, $scheduledAt, $options = [])
    {
        // This would typically integrate with social media scheduling services
        // like Hootsuite, Buffer, or direct API scheduling
        
        $scheduledPost = [
            'platform' => $platform,
            'content' => $content,
            'scheduled_at' => $scheduledAt,
            'options' => $options,
            'status' => 'scheduled',
            'created_at' => now(),
        ];

        // Store in database or external service
        // For now, just log it
        Log::info('Scheduled social media post', $scheduledPost);

        return $scheduledPost;
    }

    /**
     * Get social media content suggestions
     */
    public function getContentSuggestions($type = 'general')
    {
        $suggestions = [
            'general' => [
                'Before and after transformations',
                'New service announcements',
                'Staff spotlights',
                'Beauty tips and tricks',
                'Client testimonials',
                'Seasonal promotions',
                'Behind-the-scenes content',
                'Product recommendations',
            ],
            'facebook' => [
                'Event announcements',
                'Live Q&A sessions',
                'Client success stories',
                'Educational content',
                'Community engagement posts',
            ],
            'instagram' => [
                'Visual transformations',
                'Story highlights',
                'Reels with beauty tips',
                'IGTV tutorials',
                'User-generated content',
            ],
            'twitter' => [
                'Quick beauty tips',
                'Industry news',
                'Real-time updates',
                'Engagement with followers',
            ],
        ];

        return $suggestions[$type] ?? $suggestions['general'];
    }
}
