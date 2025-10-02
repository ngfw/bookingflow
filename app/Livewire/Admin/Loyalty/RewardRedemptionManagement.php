<?php

namespace App\Livewire\Admin\Loyalty;

use Livewire\Component;
use App\Models\Client;
use App\Models\RewardRedemption;
use App\Models\LoyaltyPoint;
use App\Models\Staff;
use Carbon\Carbon;

class RewardRedemptionManagement extends Component
{
    public $clients = [];
    public $staff = [];
    public $rewardRedemptions = [];
    public $selectedClient = '';
    public $selectedRewardType = '';
    public $selectedStatus = '';
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;
    public $showRedeemModal = false;
    public $selectedReward = null;

    // Form properties
    public $formClientId = '';
    public $formRewardType = 'discount';
    public $formRewardName = '';
    public $formDescription = '';
    public $formPointsRequired = '';
    public $formDiscountAmount = '';
    public $formDiscountPercentage = '';
    public $formCashValue = '';
    public $formExpiryDate = '';
    public $formNotes = '';
    public $formMetadata = '';

    // Redeem form properties
    public $redeemStaffId = '';
    public $redeemAppointmentId = '';
    public $redeemInvoiceId = '';
    public $redeemNotes = '';

    // Statistics
    public $totalCreated = 0;
    public $pendingCount = 0;
    public $approvedCount = 0;
    public $redeemedCount = 0;
    public $expiredCount = 0;
    public $totalPointsUsed = 0;
    public $totalValueRedeemed = 0;

    public function mount()
    {
        $this->loadClients();
        $this->loadStaff();
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
        
        // Set default date range to last 30 days
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function loadClients()
    {
        $this->clients = Client::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('staff.*')
            ->get();
    }

    public function loadRewardRedemptions()
    {
        $query = RewardRedemption::with(['client.user', 'redeemedByStaff.user', 'appointment', 'invoice']);
        
        if ($this->selectedClient) {
            $query->where('client_id', $this->selectedClient);
        }
        
        if ($this->selectedRewardType) {
            $query->where('reward_type', $this->selectedRewardType);
        }
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate . ' 23:59:59');
        }
        
        $this->rewardRedemptions = $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    public function calculateStatistics()
    {
        $stats = RewardRedemption::getRewardStatistics($this->startDate, $this->endDate);
        
        $this->totalCreated = $stats['total_created'];
        $this->pendingCount = $stats['pending'];
        $this->approvedCount = $stats['approved'];
        $this->redeemedCount = $stats['redeemed'];
        $this->expiredCount = $stats['expired'];
        $this->totalPointsUsed = $stats['total_points_used'];
        $this->totalValueRedeemed = $stats['total_value_redeemed'];
    }

    public function updatedSelectedClient()
    {
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
    }

    public function updatedSelectedRewardType()
    {
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
    }

    public function updatedSelectedStatus()
    {
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
    }

    public function updatedStartDate()
    {
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
    }

    public function updatedEndDate()
    {
        $this->loadRewardRedemptions();
        $this->calculateStatistics();
    }

    public function showCreateRewardModal()
    {
        $this->resetForm();
        $this->formRewardType = 'discount';
        $this->formExpiryDate = Carbon::now()->addMonths(6)->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function showRedeemRewardModal($rewardId)
    {
        $this->selectedReward = RewardRedemption::findOrFail($rewardId);
        $this->redeemStaffId = '';
        $this->redeemAppointmentId = '';
        $this->redeemInvoiceId = '';
        $this->redeemNotes = '';
        $this->showRedeemModal = true;
    }

    public function createReward()
    {
        $this->validate([
            'formClientId' => 'required|exists:clients,id',
            'formRewardType' => 'required|in:discount,product,service,cash_back,gift_card',
            'formRewardName' => 'required|string|max:255',
            'formDescription' => 'nullable|string|max:500',
            'formPointsRequired' => 'required|integer|min:1',
            'formDiscountAmount' => 'nullable|numeric|min:0',
            'formDiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'formCashValue' => 'nullable|numeric|min:0',
            'formExpiryDate' => 'nullable|date|after:today',
            'formNotes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if client has enough points
            $clientBalance = LoyaltyPoint::getClientBalance($this->formClientId);
            if ($clientBalance < $this->formPointsRequired) {
                session()->flash('error', "Client has insufficient points. Available: {$clientBalance}, Required: {$this->formPointsRequired}");
                return;
            }

            RewardRedemption::createReward(
                $this->formClientId,
                $this->formRewardType,
                $this->formRewardName,
                $this->formPointsRequired,
                [
                    'description' => $this->formDescription,
                    'discount_amount' => $this->formDiscountAmount ?: null,
                    'discount_percentage' => $this->formDiscountPercentage ?: null,
                    'cash_value' => $this->formCashValue ?: null,
                    'expiry_date' => $this->formExpiryDate ?: null,
                    'notes' => $this->formNotes,
                    'metadata' => $this->formMetadata ? json_decode($this->formMetadata, true) : null,
                ]
            );

            session()->flash('success', 'Reward created successfully.');
            $this->closeCreateModal();
            $this->loadRewardRedemptions();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating reward: ' . $e->getMessage());
        }
    }

    public function approveReward($rewardId)
    {
        try {
            $reward = RewardRedemption::findOrFail($rewardId);
            $reward->approve();
            
            session()->flash('success', 'Reward approved successfully.');
            $this->loadRewardRedemptions();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error approving reward: ' . $e->getMessage());
        }
    }

    public function redeemReward()
    {
        $this->validate([
            'redeemStaffId' => 'required|exists:staff,id',
            'redeemNotes' => 'nullable|string|max:500',
        ]);

        try {
            $this->selectedReward->redeem(
                $this->redeemStaffId,
                $this->redeemAppointmentId ?: null,
                $this->redeemInvoiceId ?: null,
                $this->redeemNotes
            );
            
            session()->flash('success', 'Reward redeemed successfully.');
            $this->closeRedeemModal();
            $this->loadRewardRedemptions();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error redeeming reward: ' . $e->getMessage());
        }
    }

    public function cancelReward($rewardId)
    {
        try {
            $reward = RewardRedemption::findOrFail($rewardId);
            $reward->cancel('Cancelled by admin');
            
            session()->flash('success', 'Reward cancelled successfully.');
            $this->loadRewardRedemptions();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error cancelling reward: ' . $e->getMessage());
        }
    }

    public function processExpiredRewards()
    {
        try {
            $expiredCount = RewardRedemption::processExpiredRewards();
            session()->flash('success', "Processed {$expiredCount} expired rewards.");
            $this->loadRewardRedemptions();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing expired rewards: ' . $e->getMessage());
        }
    }

    public function getClientBalance($clientId)
    {
        return LoyaltyPoint::getClientBalance($clientId);
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closeRedeemModal()
    {
        $this->showRedeemModal = false;
        $this->selectedReward = null;
        $this->redeemStaffId = '';
        $this->redeemAppointmentId = '';
        $this->redeemInvoiceId = '';
        $this->redeemNotes = '';
    }

    public function resetForm()
    {
        $this->formClientId = '';
        $this->formRewardType = 'discount';
        $this->formRewardName = '';
        $this->formDescription = '';
        $this->formPointsRequired = '';
        $this->formDiscountAmount = '';
        $this->formDiscountPercentage = '';
        $this->formCashValue = '';
        $this->formExpiryDate = '';
        $this->formNotes = '';
        $this->formMetadata = '';
    }

    public function render()
    {
        return view('livewire.admin.loyalty.reward-redemption-management')->layout('layouts.admin');
    }
}