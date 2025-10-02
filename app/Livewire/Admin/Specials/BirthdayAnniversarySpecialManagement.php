<?php

namespace App\Livewire\Admin\Specials;

use Livewire\Component;
use App\Models\BirthdayAnniversarySpecial;
use App\Models\ClientSpecialUsage;

class BirthdayAnniversarySpecialManagement extends Component
{
    public function render()
    {
        $stats = $this->getStats();
        $upcomingEvents = $this->getUpcomingEvents();
        $specials = $this->getSpecials();
        
        return view('livewire.admin.specials.birthday-anniversary-special-management', compact('stats', 'upcomingEvents', 'specials'))
            ->layout('layouts.admin');
    }
    
    private function getStats()
    {
        // Get total specials
        $totalSpecials = BirthdayAnniversarySpecial::count();
        
        // Get active specials
        $activeSpecials = BirthdayAnniversarySpecial::where('status', 'active')->count();
        
        // Get usage stats  
        $totalUsage = ClientSpecialUsage::whereHas('special', function($query) {
            $query->whereIn('type', ['birthday', 'anniversary', 'both']);
        })->count();
        $totalDiscountGiven = ClientSpecialUsage::whereHas('special', function($query) {
            $query->whereIn('type', ['birthday', 'anniversary', 'both']);
        })->sum('discount_amount') ?? 0;
        
        // Get type-specific counts
        $birthdaySpecials = BirthdayAnniversarySpecial::where('type', 'birthday')->count();
        $anniversarySpecials = BirthdayAnniversarySpecial::where('type', 'anniversary')->count();
        $bothSpecials = BirthdayAnniversarySpecial::where('type', 'both')->count();
        
        return [
            'total_specials' => $totalSpecials,
            'active' => $activeSpecials,
            'total_usage' => $totalUsage,
            'total_discount_given' => $totalDiscountGiven,
            'birthday_specials' => $birthdaySpecials,
            'anniversary_specials' => $anniversarySpecials,
            'both_specials' => $bothSpecials,
        ];
    }
    
    private function getUpcomingEvents()
    {
        return BirthdayAnniversarySpecial::getUpcomingEvents(30);
    }
    
    private function getSpecials()
    {
        return BirthdayAnniversarySpecial::orderBy('created_at', 'desc')->paginate(10);
    }
}
