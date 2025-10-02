<?php

namespace App\Livewire\Admin\Membership;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MembershipTier;
use App\Models\ClientMembership;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipTierManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $tierFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Form properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showAssignModal = false;
    public $showUpgradeModal = false;
    public $showDowngradeModal = false;

    public $tierId;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $color = '#6B7280';
    public $icon = '';
    public $minPoints = 0;
    public $maxPoints = null;
    public $minSpent = 0;
    public $maxSpent = null;
    public $minVisits = 0;
    public $maxVisits = null;
    public $discountPercentage = 0;
    public $discountAmount = 0;
    public $bonusPointsMultiplier = 1;
    public $freeShipping = false;
    public $priorityBooking = false;
    public $exclusiveServices = false;
    public $birthdayBonus = false;
    public $anniversaryBonus = false;
    public $benefits = [];
    public $restrictions = [];
    public $isActive = true;
    public $sortOrder = 0;

    public $selectedClient;
    public $selectedTier;
    public $assignmentReason = '';
    public $upgradeReason = '';
    public $downgradeReason = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:membership_tiers,slug',
        'description' => 'nullable|string',
        'color' => 'required|string|max:7',
        'icon' => 'nullable|string|max:255',
        'minPoints' => 'required|integer|min:0',
        'maxPoints' => 'nullable|integer|min:0',
        'minSpent' => 'required|numeric|min:0',
        'maxSpent' => 'nullable|numeric|min:0',
        'minVisits' => 'required|integer|min:0',
        'maxVisits' => 'nullable|integer|min:0',
        'discountPercentage' => 'required|numeric|min:0|max:100',
        'discountAmount' => 'required|numeric|min:0',
        'bonusPointsMultiplier' => 'required|numeric|min:1',
        'freeShipping' => 'boolean',
        'priorityBooking' => 'boolean',
        'exclusiveServices' => 'boolean',
        'birthdayBonus' => 'boolean',
        'anniversaryBonus' => 'boolean',
        'benefits' => 'nullable|array',
        'restrictions' => 'nullable|array',
        'isActive' => 'boolean',
        'sortOrder' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = MembershipTier::orderBy('sort_order');

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $tiers = $query->paginate(20);

        // Statistics
        $stats = $this->getStatistics();

        // Top tiers
        $topTiers = ClientMembership::getTopTiers(5);

        // Clients for assignment
        $clients = Client::with('user')->get();

        return view('livewire.admin.membership.membership-tier-management', [
            'tiers' => $tiers,
            'stats' => $stats,
            'topTiers' => $topTiers,
            'clients' => $clients,
        ])->layout('layouts.admin');
    }

    public function getStatistics()
    {
        $query = ClientMembership::query();

        if ($this->dateFrom) {
            $query->where('start_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('start_date', '<=', $this->dateTo . ' 23:59:59');
        }

        return ClientMembership::getMembershipStatistics(
            $this->dateFrom ? Carbon::parse($this->dateFrom) : null,
            $this->dateTo ? Carbon::parse($this->dateTo . ' 23:59:59') : null
        );
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($tierId)
    {
        $tier = MembershipTier::findOrFail($tierId);
        
        $this->tierId = $tier->id;
        $this->name = $tier->name;
        $this->slug = $tier->slug;
        $this->description = $tier->description;
        $this->color = $tier->color;
        $this->icon = $tier->icon;
        $this->minPoints = $tier->min_points;
        $this->maxPoints = $tier->max_points;
        $this->minSpent = $tier->min_spent;
        $this->maxSpent = $tier->max_spent;
        $this->minVisits = $tier->min_visits;
        $this->maxVisits = $tier->max_visits;
        $this->discountPercentage = $tier->discount_percentage;
        $this->discountAmount = $tier->discount_amount;
        $this->bonusPointsMultiplier = $tier->bonus_points_multiplier;
        $this->freeShipping = $tier->free_shipping;
        $this->priorityBooking = $tier->priority_booking;
        $this->exclusiveServices = $tier->exclusive_services;
        $this->birthdayBonus = $tier->birthday_bonus;
        $this->anniversaryBonus = $tier->anniversary_bonus;
        $this->benefits = $tier->benefits ?? [];
        $this->restrictions = $tier->restrictions ?? [];
        $this->isActive = $tier->is_active;
        $this->sortOrder = $tier->sort_order;

        $this->showEditModal = true;
    }

    public function createTier()
    {
        $this->validate();

        try {
            MembershipTier::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'color' => $this->color,
                'icon' => $this->icon,
                'min_points' => $this->minPoints,
                'max_points' => $this->maxPoints,
                'min_spent' => $this->minSpent,
                'max_spent' => $this->maxSpent,
                'min_visits' => $this->minVisits,
                'max_visits' => $this->maxVisits,
                'discount_percentage' => $this->discountPercentage,
                'discount_amount' => $this->discountAmount,
                'bonus_points_multiplier' => $this->bonusPointsMultiplier,
                'free_shipping' => $this->freeShipping,
                'priority_booking' => $this->priorityBooking,
                'exclusive_services' => $this->exclusiveServices,
                'birthday_bonus' => $this->birthdayBonus,
                'anniversary_bonus' => $this->anniversaryBonus,
                'benefits' => $this->benefits,
                'restrictions' => $this->restrictions,
                'is_active' => $this->isActive,
                'sort_order' => $this->sortOrder,
            ]);

            session()->flash('success', 'Membership tier created successfully!');
            $this->showCreateModal = false;
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating membership tier: ' . $e->getMessage());
        }
    }

    public function createDefaultTiers()
    {
        try {
            MembershipTier::createDefaultTiers();
            session()->flash('success', 'Default membership tiers created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating default tiers: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->tierId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->color = '#6B7280';
        $this->icon = '';
        $this->minPoints = 0;
        $this->maxPoints = null;
        $this->minSpent = 0;
        $this->maxSpent = null;
        $this->minVisits = 0;
        $this->maxVisits = null;
        $this->discountPercentage = 0;
        $this->discountAmount = 0;
        $this->bonusPointsMultiplier = 1;
        $this->freeShipping = false;
        $this->priorityBooking = false;
        $this->exclusiveServices = false;
        $this->birthdayBonus = false;
        $this->anniversaryBonus = false;
        $this->benefits = [];
        $this->restrictions = [];
        $this->isActive = true;
        $this->sortOrder = 0;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->tierFilter = '';
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}