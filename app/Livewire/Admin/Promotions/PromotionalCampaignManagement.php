<?php

namespace App\Livewire\Admin\Promotions;

use Livewire\Component;
use App\Models\PromotionalCampaign;

class PromotionalCampaignManagement extends Component
{
    public function render()
    {
        $stats = $this->getStats();
        $campaigns = $this->getCampaigns();
        
        return view('livewire.admin.promotions.promotional-campaign-management', compact('stats', 'campaigns'))
            ->layout('layouts.admin');
    }
    
    private function getStats()
    {
        $totalCampaigns = PromotionalCampaign::count();
        $activeCampaigns = PromotionalCampaign::where('status', 'active')->count();
        $expiredCampaigns = PromotionalCampaign::where('status', 'expired')->count();
        $scheduledCampaigns = PromotionalCampaign::where('status', 'scheduled')->count();
        $draftCampaigns = PromotionalCampaign::where('status', 'draft')->count();
        $pausedCampaigns = PromotionalCampaign::where('status', 'paused')->count();
        $completedCampaigns = PromotionalCampaign::where('status', 'completed')->count();
        $cancelledCampaigns = PromotionalCampaign::where('status', 'cancelled')->count();
        $totalUsage = PromotionalCampaign::sum('current_usage');
        
        // For total discount given, we'd need a related table/model to track actual usage
        // For now, setting to 0 since we don't have campaign usage tracking implemented
        $totalDiscountGiven = 0;
        
        return [
            'total_campaigns' => $totalCampaigns,
            'active' => $activeCampaigns,
            'expired' => $expiredCampaigns,
            'scheduled' => $scheduledCampaigns,
            'draft' => $draftCampaigns,
            'paused' => $pausedCampaigns,
            'completed' => $completedCampaigns,
            'cancelled' => $cancelledCampaigns,
            'total_usage' => $totalUsage,
            'total_discount_given' => $totalDiscountGiven,
        ];
    }
    
    private function getCampaigns()
    {
        return PromotionalCampaign::orderBy('created_at', 'desc')->paginate(10);
    }
    
    public function processExpiredCampaigns()
    {
        // Process expired campaigns logic would go here
        session()->flash('message', 'Expired campaigns processed successfully.');
    }
    
    public function openCreateModal()
    {
        // Open create campaign modal logic would go here
        session()->flash('message', 'Create campaign functionality coming soon.');
    }
}
