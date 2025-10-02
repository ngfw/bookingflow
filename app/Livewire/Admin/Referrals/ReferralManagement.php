<?php

namespace App\Livewire\Admin\Referrals;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Referral;
use App\Models\Client;
use App\Models\LoyaltyPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReferralManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $methodFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $referrerFilter = '';
    public $referredFilter = '';

    // Form properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showCompleteModal = false;
    public $showCancelModal = false;
    public $showExpireModal = false;
    public $showClaimModal = false;

    public $referralId;
    public $referrerId = '';
    public $referredEmail = '';
    public $referredName = '';
    public $referredPhone = '';
    public $referralMethod = 'code';
    public $notes = '';
    public $expiryDate = '';
    public $referrerRewardAmount = 0;
    public $referredRewardAmount = 0;
    public $referrerPoints = 100;
    public $referredPoints = 50;
    public $metadata = [];

    public $selectedReferral;
    public $completionReason = '';
    public $cancellationReason = '';
    public $expiryReason = '';
    public $claimType = '';

    protected $rules = [
        'referrerId' => 'required|exists:clients,id',
        'referredEmail' => 'nullable|email',
        'referredName' => 'nullable|string|max:255',
        'referredPhone' => 'nullable|string|max:20',
        'referralMethod' => 'required|string',
        'notes' => 'nullable|string',
        'expiryDate' => 'nullable|date|after:today',
        'referrerRewardAmount' => 'nullable|numeric|min:0',
        'referredRewardAmount' => 'nullable|numeric|min:0',
        'referrerPoints' => 'nullable|integer|min:0',
        'referredPoints' => 'nullable|integer|min:0',
    ];

    public function mount()
    {
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Referral::with(['referrer.user', 'referred.user', 'completedAppointment', 'completedInvoice'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('referral_code', 'like', '%' . $this->search . '%')
                  ->orWhere('referred_email', 'like', '%' . $this->search . '%')
                  ->orWhere('referred_name', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->methodFilter) {
            $query->where('referral_method', $this->methodFilter);
        }

        if ($this->referrerFilter) {
            $query->where('referrer_id', $this->referrerFilter);
        }

        if ($this->referredFilter) {
            $query->where('referred_id', $this->referredFilter);
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        $referrals = $query->paginate(20);

        // Statistics
        $stats = $this->getStatistics();

        // Top referrers
        $topReferrers = Referral::getTopReferrers(5);

        // Referral methods
        $referralMethods = Referral::getReferralMethods();

        // Clients for dropdowns
        $clients = Client::with('user')->get();

        return view('livewire.admin.referrals.referral-management', [
            'referrals' => $referrals,
            'stats' => $stats,
            'topReferrers' => $topReferrers,
            'referralMethods' => $referralMethods,
            'clients' => $clients,
        ])->layout('layouts.admin');
    }

    public function getStatistics()
    {
        $query = Referral::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        return Referral::getReferralStatistics(
            $this->dateFrom ? Carbon::parse($this->dateFrom) : null,
            $this->dateTo ? Carbon::parse($this->dateTo . ' 23:59:59') : null
        );
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($referralId)
    {
        $referral = Referral::findOrFail($referralId);
        
        $this->referralId = $referral->id;
        $this->referrerId = $referral->referrer_id;
        $this->referredEmail = $referral->referred_email;
        $this->referredName = $referral->referred_name;
        $this->referredPhone = $referral->referred_phone;
        $this->referralMethod = $referral->referral_method;
        $this->notes = $referral->notes;
        $this->expiryDate = $referral->expiry_date?->format('Y-m-d');
        $this->referrerRewardAmount = $referral->referrer_reward_amount;
        $this->referredRewardAmount = $referral->referred_reward_amount;
        $this->referrerPoints = $referral->referrer_points;
        $this->referredPoints = $referral->referred_points;
        $this->metadata = $referral->metadata ?? [];

        $this->showEditModal = true;
    }

    public function openCompleteModal($referralId)
    {
        $this->selectedReferral = Referral::findOrFail($referralId);
        $this->showCompleteModal = true;
    }

    public function openCancelModal($referralId)
    {
        $this->selectedReferral = Referral::findOrFail($referralId);
        $this->showCancelModal = true;
    }

    public function openExpireModal($referralId)
    {
        $this->selectedReferral = Referral::findOrFail($referralId);
        $this->showExpireModal = true;
    }

    public function openClaimModal($referralId, $claimType)
    {
        $this->selectedReferral = Referral::findOrFail($referralId);
        $this->claimType = $claimType;
        $this->showClaimModal = true;
    }

    public function createReferral()
    {
        $this->validate();

        try {
            $referralData = [
                'email' => $this->referredEmail,
                'name' => $this->referredName,
                'phone' => $this->referredPhone,
            ];

            $options = [
                'method' => $this->referralMethod,
                'notes' => $this->notes,
                'expiry_date' => $this->expiryDate ? Carbon::parse($this->expiryDate) : null,
                'referrer_reward' => $this->referrerRewardAmount,
                'referred_reward' => $this->referredRewardAmount,
                'referrer_points' => $this->referrerPoints,
                'referred_points' => $this->referredPoints,
                'metadata' => $this->metadata,
            ];

            Referral::createReferral($this->referrerId, $referralData, $options);

            session()->flash('success', 'Referral created successfully!');
            $this->showCreateModal = false;
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating referral: ' . $e->getMessage());
        }
    }

    public function updateReferral()
    {
        $this->validate([
            'referrerId' => 'required|exists:clients,id',
            'referredEmail' => 'nullable|email',
            'referredName' => 'nullable|string|max:255',
            'referredPhone' => 'nullable|string|max:20',
            'referralMethod' => 'required|string',
            'notes' => 'nullable|string',
            'expiryDate' => 'nullable|date',
            'referrerRewardAmount' => 'nullable|numeric|min:0',
            'referredRewardAmount' => 'nullable|numeric|min:0',
            'referrerPoints' => 'nullable|integer|min:0',
            'referredPoints' => 'nullable|integer|min:0',
        ]);

        try {
            $referral = Referral::findOrFail($this->referralId);
            
            $referral->update([
                'referrer_id' => $this->referrerId,
                'referred_email' => $this->referredEmail,
                'referred_name' => $this->referredName,
                'referred_phone' => $this->referredPhone,
                'referral_method' => $this->referralMethod,
                'notes' => $this->notes,
                'expiry_date' => $this->expiryDate ? Carbon::parse($this->expiryDate) : null,
                'referrer_reward_amount' => $this->referrerRewardAmount,
                'referred_reward_amount' => $this->referredRewardAmount,
                'referrer_points' => $this->referrerPoints,
                'referred_points' => $this->referredPoints,
                'metadata' => $this->metadata,
            ]);

            session()->flash('success', 'Referral updated successfully!');
            $this->showEditModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating referral: ' . $e->getMessage());
        }
    }

    public function completeReferral()
    {
        $this->validate([
            'completionReason' => 'required|string|max:255',
        ]);

        try {
            $this->selectedReferral->complete(
                $this->selectedReferral->referred_id,
                null,
                null,
                $this->completionReason
            );

            session()->flash('success', 'Referral completed successfully!');
            $this->showCompleteModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error completing referral: ' . $e->getMessage());
        }
    }

    public function cancelReferral()
    {
        $this->validate([
            'cancellationReason' => 'required|string|max:255',
        ]);

        try {
            $this->selectedReferral->cancel($this->cancellationReason);

            session()->flash('success', 'Referral cancelled successfully!');
            $this->showCancelModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error cancelling referral: ' . $e->getMessage());
        }
    }

    public function expireReferral()
    {
        $this->validate([
            'expiryReason' => 'required|string|max:255',
        ]);

        try {
            $this->selectedReferral->expire();

            session()->flash('success', 'Referral expired successfully!');
            $this->showExpireModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error expiring referral: ' . $e->getMessage());
        }
    }

    public function claimReward()
    {
        try {
            if ($this->claimType === 'referrer') {
                $this->selectedReferral->claimReferrerReward();
                session()->flash('success', 'Referrer reward claimed successfully!');
            } elseif ($this->claimType === 'referred') {
                $this->selectedReferral->claimReferredReward();
                session()->flash('success', 'Referred reward claimed successfully!');
            }

            $this->showClaimModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error claiming reward: ' . $e->getMessage());
        }
    }

    public function processExpiredReferrals()
    {
        try {
            $count = Referral::processExpiredReferrals();
            session()->flash('success', "Processed {$count} expired referrals.");
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing expired referrals: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->referralId = null;
        $this->referrerId = '';
        $this->referredEmail = '';
        $this->referredName = '';
        $this->referredPhone = '';
        $this->referralMethod = 'code';
        $this->notes = '';
        $this->expiryDate = '';
        $this->referrerRewardAmount = 0;
        $this->referredRewardAmount = 0;
        $this->referrerPoints = 100;
        $this->referredPoints = 50;
        $this->metadata = [];
        $this->completionReason = '';
        $this->cancellationReason = '';
        $this->expiryReason = '';
        $this->claimType = '';
        $this->selectedReferral = null;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->methodFilter = '';
        $this->referrerFilter = '';
        $this->referredFilter = '';
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}