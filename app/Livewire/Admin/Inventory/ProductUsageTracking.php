<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductUsage;
use App\Models\Product;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class ProductUsageTracking extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFilter = '';
    public $productFilter = '';
    public $staffFilter = '';
    public $sortField = 'usage_date';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Form fields for adding new usage
    public $showAddForm = false;
    public $product_id = '';
    public $appointment_id = '';
    public $staff_id = '';
    public $quantity_used = '';
    public $cost_per_unit = '';
    public $notes = '';
    public $usage_date = '';

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'appointment_id' => 'nullable|exists:appointments,id',
        'staff_id' => 'required|exists:staff,id',
        'quantity_used' => 'required|integer|min:1',
        'cost_per_unit' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:1000',
        'usage_date' => 'required|date',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingProductFilter()
    {
        $this->resetPage();
    }

    public function updatingStaffFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function showAddForm()
    {
        $this->showAddForm = true;
        $this->usage_date = Carbon::today()->format('Y-m-d');
    }

    public function hideAddForm()
    {
        $this->showAddForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->product_id = '';
        $this->appointment_id = '';
        $this->staff_id = '';
        $this->quantity_used = '';
        $this->cost_per_unit = '';
        $this->notes = '';
        $this->usage_date = '';
    }

    public function updatedProductId()
    {
        if ($this->product_id) {
            $product = Product::find($this->product_id);
            if ($product) {
                $this->cost_per_unit = $product->cost_price;
            }
        }
    }

    public function updatedQuantityUsed()
    {
        $this->calculateTotalCost();
    }

    public function updatedCostPerUnit()
    {
        $this->calculateTotalCost();
    }

    public function calculateTotalCost()
    {
        if ($this->quantity_used && $this->cost_per_unit) {
            $this->total_cost = $this->quantity_used * $this->cost_per_unit;
        }
    }

    public function save()
    {
        $this->validate();

        // Check if product has enough stock
        $product = Product::find($this->product_id);
        if ($product->current_stock < $this->quantity_used) {
            session()->flash('error', "Insufficient stock. Available: {$product->current_stock}, Required: {$this->quantity_used}");
            return;
        }

        // Create product usage record
        ProductUsage::create([
            'product_id' => $this->product_id,
            'appointment_id' => $this->appointment_id,
            'staff_id' => $this->staff_id,
            'quantity_used' => $this->quantity_used,
            'cost_per_unit' => $this->cost_per_unit,
            'total_cost' => $this->quantity_used * $this->cost_per_unit,
            'notes' => $this->notes,
            'usage_date' => $this->usage_date,
        ]);

        // Update product stock
        $product->decrement('current_stock', $this->quantity_used);

        session()->flash('success', 'Product usage recorded successfully!');
        $this->hideAddForm();
    }

    public function deleteUsage($usageId)
    {
        $usage = ProductUsage::findOrFail($usageId);
        
        // Restore stock
        $product = $usage->product;
        $product->increment('current_stock', $usage->quantity_used);
        
        // Delete usage record
        $usage->delete();

        session()->flash('success', 'Product usage deleted and stock restored.');
    }

    public function render()
    {
        $usage = ProductUsage::with(['product', 'appointment.client.user', 'staff.user'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                })->orWhereHas('appointment.client.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('usage_date', Carbon::today());
                } elseif ($this->dateFilter === 'yesterday') {
                    $query->whereDate('usage_date', Carbon::yesterday());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('usage_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'this_month') {
                    $query->whereBetween('usage_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                }
            })
            ->when($this->productFilter, function ($query) {
                $query->where('product_id', $this->productFilter);
            })
            ->when($this->staffFilter, function ($query) {
                $query->where('staff_id', $this->staffFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $appointments = Appointment::with(['client.user', 'service'])
            ->where('status', 'completed')
            ->whereDate('appointment_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('appointment_date', 'desc')
            ->get();
        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->orderBy('user_id')->get();

        return view('livewire.admin.inventory.product-usage-tracking', [
            'usage' => $usage,
            'products' => $products,
            'appointments' => $appointments,
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
