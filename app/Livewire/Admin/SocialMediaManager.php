<?php

namespace App\Livewire\Admin;

use App\Services\SocialMediaService;
use Livewire\Component;
use Livewire\WithFileUploads;

class SocialMediaManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'settings';
    public $showModal = false;
    public $modalType = '';

    // Social media links
    public $facebook_url = '';
    public $instagram_url = '';
    public $twitter_url = '';
    public $youtube_url = '';
    public $tiktok_url = '';
    public $linkedin_url = '';
    public $pinterest_url = '';

    // Post scheduling
    public $post_platform = 'facebook';
    public $post_content = '';
    public $post_image;
    public $post_scheduled_at = '';
    public $post_link = '';

    // Analytics
    public $analyticsData = [];
    public $selectedPeriod = '7d';

    protected $socialMediaService;

    public function mount()
    {
        $this->socialMediaService = new SocialMediaService();
        $this->loadSocialLinks();
        $this->loadAnalytics();
    }

    public function loadSocialLinks()
    {
        $links = $this->socialMediaService->getSocialLinks();
        $this->facebook_url = $links['facebook'] ?? '';
        $this->instagram_url = $links['instagram'] ?? '';
        $this->twitter_url = $links['twitter'] ?? '';
        $this->youtube_url = $links['youtube'] ?? '';
        $this->tiktok_url = $links['tiktok'] ?? '';
        $this->linkedin_url = $links['linkedin'] ?? '';
        $this->pinterest_url = $links['pinterest'] ?? '';
    }

    public function loadAnalytics()
    {
        $this->analyticsData = $this->socialMediaService->getAnalyticsSummary();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
        $this->resetForm();
    }

    public function saveSocialLinks()
    {
        $links = [
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'twitter' => $this->twitter_url,
            'youtube' => $this->youtube_url,
            'tiktok' => $this->tiktok_url,
            'linkedin' => $this->linkedin_url,
            'pinterest' => $this->pinterest_url,
        ];

        $this->socialMediaService->updateSocialLinks($links);
        $this->closeModal();
        session()->flash('success', 'Social media links updated successfully!');
    }

    public function schedulePost()
    {
        $this->validate([
            'post_platform' => 'required|string',
            'post_content' => 'required|string',
            'post_scheduled_at' => 'required|date|after:now',
        ]);

        $options = [];
        if ($this->post_link) {
            $options['link'] = $this->post_link;
        }
        if ($this->post_image) {
            $options['image'] = $this->post_image->store('social-media', 'public');
        }

        $this->socialMediaService->schedulePost(
            $this->post_platform,
            $this->post_content,
            $this->post_scheduled_at,
            $options
        );

        $this->closeModal();
        session()->flash('success', 'Post scheduled successfully!');
    }

    public function resetForm()
    {
        $this->post_platform = 'facebook';
        $this->post_content = '';
        $this->post_image = null;
        $this->post_scheduled_at = '';
        $this->post_link = '';
    }

    public function getContentSuggestions($platform = 'general')
    {
        return $this->socialMediaService->getContentSuggestions($platform);
    }

    public function render()
    {
        return view('livewire.admin.social-media-manager', [
            'contentSuggestions' => $this->getContentSuggestions(),
            'platformSuggestions' => [
                'facebook' => $this->getContentSuggestions('facebook'),
                'instagram' => $this->getContentSuggestions('instagram'),
                'twitter' => $this->getContentSuggestions('twitter'),
            ],
        ]);
    }
}
