<?php

namespace App\Livewire\Admin\Loyalty;

use Livewire\Component;
use App\Models\Client;
use App\Models\LoyaltyPoint;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;

class LoyaltyPointsManagement extends Component
{
    public $clients = [];
    public $loyaltyPoints = [];
    public $selectedClient = '';
    public $selectedTransactionType = '';
    public $selectedSource = '';
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;
    public $showAdjustModal = false;
    public $showExpireModal = false;

    // Form properties
    public $formClientId = '';
    public $formAppointmentId = '';
    public $formInvoiceId = '';
    public $formTransactionType = 'earned';
    public $formPoints = '';
    public $formSource = 'appointment';
    public $formDescription = '';
    public $formTransactionValue = '';
    public $formPointsPerDollar = 1.00;
    public $formExpiryDate = '';
    public $formMetadata = '';

    // Statistics
    public $totalEarned = 0;
    public $totalRedeemed = 0;
    public $totalExpired = 0;
    public $activeBalance = 0;
    public $expiringSoon = 0;
    public $topClients = [];

    public function mount()
    {
        $this->loadClients();
        $this->loadLoyaltyPoints();
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

    public function loadLoyaltyPoints()
    {
        $query = LoyaltyPoint::with(['client.user', 'appointment', 'invoice']);
        
        if ($this->selectedClient) {
            $query->where('client_id', $this->selectedClient);
        }
        
        if ($this->selectedTransactionType) {
            $query->where('transaction_type', $this->selectedTransactionType);
        }
        
        if ($this->selectedSource) {
            $query->where('source', $this->selectedSource);
        }
        
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate . ' 23:59:59');
        }
        
        $this->loyaltyPoints = $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    public function calculateStatistics()
    {
        $stats = LoyaltyPoint::getLoyaltyStatistics($this->startDate, $this->endDate);
        
        $this->totalEarned = $stats['total_earned'];
        $this->totalRedeemed = $stats['total_redeemed'];
        $this->totalExpired = $stats['total_expired'];
        $this->activeBalance = $stats['active_balance'];
        $this->expiringSoon = $stats['expiring_soon'];
        
        $this->topClients = LoyaltyPoint::getTopClientsByPoints(5);
    }

    public function updatedSelectedClient()
    {
        $this->loadLoyaltyPoints();
        $this->calculateStatistics();
    }

    public function updatedSelectedTransactionType()
    {
        $this->loadLoyaltyPoints();
        $this->calculateStatistics();
    }

    public function updatedSelectedSource()
    {
        $this->loadLoyaltyPoints();
        $this->calculateStatistics();
    }

    public function updatedStartDate()
    {
        $this->loadLoyaltyPoints();
        $this->calculateStatistics();
    }

    public function updatedEndDate()
    {
        $this->loadLoyaltyPoints();
        $this->calculateStatistics();
    }

    public function showCreatePointsModal()
    {
        $this->resetForm();
        $this->formTransactionType = 'earned';
        $this->formSource = 'appointment';
        $this->formPointsPerDollar = 1.00;
        $this->formExpiryDate = Carbon::now()->addYear()->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function showAdjustPointsModal()
    {
        $this->resetForm();
        $this->formTransactionType = 'adjusted';
        $this->formSource = 'manual_adjustment';
        $this->showAdjustModal = true;
    }

    public function showExpirePointsModal()
    {
        $this->resetForm();
        $this->formTransactionType = 'expired';
        $this->formSource = 'expiration';
        $this->showExpireModal = true;
    }

    public function createLoyaltyPoint()
    {
        $this->validate([
            'formClientId' => 'required|exists:clients,id',
            'formTransactionType' => 'required|in:earned,redeemed,adjusted,expired',
            'formPoints' => 'required|integer',
            'formSource' => 'required|string|max:255',
            'formDescription' => 'nullable|string|max:500',
            'formTransactionValue' => 'nullable|numeric|min:0',
            'formPointsPerDollar' => 'nullable|numeric|min:0',
            'formExpiryDate' => 'nullable|date|after:today',
        ]);

        try {
            if ($this->formTransactionType === 'earned') {
                LoyaltyPoint::earnPoints(
                    $this->formClientId,
                    $this->formPoints,
                    $this->formSource,
                    [
                        'appointment_id' => $this->formAppointmentId ?: null,
                        'invoice_id' => $this->formInvoiceId ?: null,
                        'description' => $this->formDescription,
                        'transaction_value' => $this->formTransactionValue ?: null,
                        'points_per_dollar' => $this->formPointsPerDollar,
                        'expiry_date' => $this->formExpiryDate ?: null,
                        'metadata' => $this->formMetadata ? json_decode($this->formMetadata, true) : null,
                    ]
                );
            } elseif ($this->formTransactionType === 'redeemed') {
                LoyaltyPoint::redeemPoints(
                    $this->formClientId,
                    $this->formPoints,
                    $this->formDescription,
                    [
                        'appointment_id' => $this->formAppointmentId ?: null,
                        'invoice_id' => $this->formInvoiceId ?: null,
                        'transaction_value' => $this->formTransactionValue ?: null,
                        'points_per_dollar' => $this->formPointsPerDollar,
                        'metadata' => $this->formMetadata ? json_decode($this->formMetadata, true) : null,
                    ]
                );
            } elseif ($this->formTransactionType === 'adjusted') {
                LoyaltyPoint::adjustPoints(
                    $this->formClientId,
                    $this->formPoints,
                    $this->formDescription,
                    [
                        'appointment_id' => $this->formAppointmentId ?: null,
                        'invoice_id' => $this->formInvoiceId ?: null,
                        'transaction_value' => $this->formTransactionValue ?: null,
                        'points_per_dollar' => $this->formPointsPerDollar,
                        'expiry_date' => $this->formExpiryDate ?: null,
                        'metadata' => $this->formMetadata ? json_decode($this->formMetadata, true) : null,
                    ]
                );
            } elseif ($this->formTransactionType === 'expired') {
                LoyaltyPoint::expirePoints(
                    $this->formClientId,
                    $this->formPoints,
                    $this->formDescription
                );
            }

            session()->flash('success', 'Loyalty point transaction created successfully.');
            $this->closeCreateModal();
            $this->loadLoyaltyPoints();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating loyalty point transaction: ' . $e->getMessage());
        }
    }

    public function processExpiredPoints()
    {
        try {
            $expiredCount = LoyaltyPoint::processExpiredPoints();
            session()->flash('success', "Processed {$expiredCount} expired points.");
            $this->loadLoyaltyPoints();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing expired points: ' . $e->getMessage());
        }
    }

    public function getClientBalance($clientId)
    {
        return LoyaltyPoint::getClientBalance($clientId);
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->showAdjustModal = false;
        $this->showExpireModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->formClientId = '';
        $this->formAppointmentId = '';
        $this->formInvoiceId = '';
        $this->formTransactionType = 'earned';
        $this->formPoints = '';
        $this->formSource = 'appointment';
        $this->formDescription = '';
        $this->formTransactionValue = '';
        $this->formPointsPerDollar = 1.00;
        $this->formExpiryDate = '';
        $this->formMetadata = '';
    }

    public function render()
    {
        return view('livewire.admin.loyalty.loyalty-points-management')->layout('layouts.admin');
    }
}