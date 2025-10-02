<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashDrawer;
use App\Models\User;
use Carbon\Carbon;

class CashDrawerManagement extends Component
{
    use WithPagination;

    public $showOpenModal = false;
    public $showCloseModal = false;
    public $showHistoryModal = false;
    public $currentDrawer = null;
    public $selectedDrawer = null;

    // Open drawer form
    public $openingAmount = 0;
    public $openingNotes = '';

    // Close drawer form
    public $closingAmount = 0;
    public $closingNotes = '';

    public function mount()
    {
        $this->currentDrawer = CashDrawer::getCurrentDrawer();
        if ($this->currentDrawer) {
            $this->closingAmount = $this->currentDrawer->calculateExpectedAmount();
        }
    }

    public function openDrawer()
    {
        $this->validate([
            'openingAmount' => 'required|numeric|min:0',
            'openingNotes' => 'nullable|string|max:500',
        ]);

        try {
            CashDrawer::openDrawer(auth()->id(), $this->openingAmount, $this->openingNotes);
            
            $this->currentDrawer = CashDrawer::getCurrentDrawer();
            $this->showOpenModal = false;
            $this->reset(['openingAmount', 'openingNotes']);
            
            session()->flash('success', 'Cash drawer opened successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function closeDrawer()
    {
        $this->validate([
            'closingAmount' => 'required|numeric|min:0',
            'closingNotes' => 'nullable|string|max:500',
        ]);

        if (!$this->currentDrawer) {
            session()->flash('error', 'No open cash drawer found.');
            return;
        }

        try {
            $this->currentDrawer->close($this->closingAmount, $this->closingNotes);
            
            $this->currentDrawer = null;
            $this->showCloseModal = false;
            $this->reset(['closingAmount', 'closingNotes']);
            
            session()->flash('success', 'Cash drawer closed successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function showOpenModal()
    {
        $this->showOpenModal = true;
    }

    public function hideOpenModal()
    {
        $this->showOpenModal = false;
        $this->reset(['openingAmount', 'openingNotes']);
    }

    public function showCloseModal()
    {
        if ($this->currentDrawer) {
            $this->closingAmount = $this->currentDrawer->calculateExpectedAmount();
            $this->showCloseModal = true;
        }
    }

    public function hideCloseModal()
    {
        $this->showCloseModal = false;
        $this->reset(['closingAmount', 'closingNotes']);
    }

    public function showHistoryModal()
    {
        $this->showHistoryModal = true;
    }

    public function hideHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedDrawer = null;
    }

    public function viewDrawerDetails($drawerId)
    {
        $this->selectedDrawer = CashDrawer::with('user')->find($drawerId);
        $this->showHistoryModal = true;
    }

    public function getDrawerHistory()
    {
        return CashDrawer::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->orderBy('opened_at', 'desc')
            ->paginate(10);
    }

    public function getTodayStats()
    {
        if (!$this->currentDrawer) {
            return [
                'total_sales' => 0,
                'cash_sales' => 0,
                'transactions' => 0,
                'expected_amount' => 0,
            ];
        }

        return [
            'total_sales' => $this->currentDrawer->getTotalRevenue(),
            'cash_sales' => $this->currentDrawer->getTotalCashSales(),
            'transactions' => $this->currentDrawer->getTotalTransactions(),
            'expected_amount' => $this->currentDrawer->calculateExpectedAmount(),
        ];
    }

    public function getPaymentMethodBreakdown()
    {
        if (!$this->currentDrawer) {
            return collect();
        }

        return $this->currentDrawer->getPaymentMethodBreakdown();
    }

    public function getDrawerStats()
    {
        $totalDrawers = CashDrawer::where('user_id', auth()->id())->count();
        $openDrawers = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'open')
            ->count();
        $closedDrawers = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->count();
        
        $totalRevenue = CashDrawer::where('user_id', auth()->id())
            ->where('status', 'closed')
            ->get()
            ->sum(function($drawer) {
                return $drawer->getTotalRevenue();
            });

        return [
            'total_drawers' => $totalDrawers,
            'open_drawers' => $openDrawers,
            'closed_drawers' => $closedDrawers,
            'total_revenue' => $totalRevenue,
        ];
    }

    public function render()
    {
        $drawerHistory = $this->getDrawerHistory();
        $todayStats = $this->getTodayStats();
        $paymentBreakdown = $this->getPaymentMethodBreakdown();
        $drawerStats = $this->getDrawerStats();

        return view('livewire.admin.pos.cash-drawer-management', [
            'drawerHistory' => $drawerHistory,
            'todayStats' => $todayStats,
            'paymentBreakdown' => $paymentBreakdown,
            'drawerStats' => $drawerStats,
        ])->layout('layouts.admin');
    }
}
