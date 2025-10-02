<?php

namespace App\Livewire\Admin\Franchises;

use Livewire\Component;
use App\Models\Franchise;
use App\Models\FranchisePayment;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showFilters = false;
    
    // Filters
    public $statusFilter = '';
    public $typeFilter = '';
    public $performanceFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->performanceFilter = '';
        $this->resetPage();
    }

    public function updateFranchiseStatus($franchiseId, $status)
    {
        $franchise = Franchise::findOrFail($franchiseId);
        $franchise->update(['status' => $status]);
        
        session()->flash('success', "Franchise status updated to {$status} successfully.");
    }

    public function generateFranchisePayment($franchiseId, $type = 'royalty')
    {
        $franchise = Franchise::findOrFail($franchiseId);
        
        // Calculate amount based on type and franchise settings
        $amount = 0;
        $dueDate = now()->addDays(30);
        
        switch ($type) {
            case 'royalty':
                $amount = $franchise->current_month_sales * $franchise->royalty_rate;
                break;
            case 'marketing_fee':
                $amount = $franchise->current_month_sales * $franchise->marketing_fee_rate;
                break;
            case 'technology_fee':
                $amount = $franchise->current_month_sales * $franchise->technology_fee_rate;
                break;
        }
        
        if ($amount > 0) {
            FranchisePayment::create([
                'franchise_id' => $franchise->id,
                'payment_type' => $type,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
            
            session()->flash('success', "Generated {$type} payment of $" . number_format($amount, 2) . " for franchise {$franchise->name}.");
        } else {
            session()->flash('error', 'No amount calculated for this payment type.');
        }
    }

    public function render()
    {
        $franchises = Franchise::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('franchise_code', 'like', '%' . $this->search . '%')
                      ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                      ->orWhere('owner_city', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('franchise_type', $this->typeFilter);
            })
            ->when($this->performanceFilter, function ($query) {
                if ($this->performanceFilter === 'high') {
                    $query->whereRaw('(current_month_sales / monthly_sales_target) >= 1.2');
                } elseif ($this->performanceFilter === 'low') {
                    $query->whereRaw('(current_month_sales / monthly_sales_target) < 0.8');
                } elseif ($this->performanceFilter === 'target') {
                    $query->whereRaw('(current_month_sales / monthly_sales_target) BETWEEN 0.8 AND 1.2');
                }
            })
            ->withCount(['locations', 'users', 'payments as overdue_payments' => function ($query) {
                $query->where('status', 'overdue');
            }])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total_franchises' => Franchise::count(),
            'active_franchises' => Franchise::where('status', 'active')->count(),
            'pending_franchises' => Franchise::where('status', 'pending')->count(),
            'suspended_franchises' => Franchise::where('status', 'suspended')->count(),
            'total_locations' => Franchise::withCount('locations')->get()->sum('locations_count'),
            'total_overdue_payments' => FranchisePayment::where('status', 'overdue')->sum('amount'),
            'agreements_expiring_soon' => Franchise::expiringSoon()->count(),
        ];

        return view('livewire.admin.franchises.index', compact('franchises', 'stats'));
    }
}