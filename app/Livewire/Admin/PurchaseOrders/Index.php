<?php

namespace App\Livewire\Admin\PurchaseOrders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $supplierFilter = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSupplierFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
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

    public function updateStatus($purchaseOrderId, $status)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
        $purchaseOrder->update(['status' => $status]);
        
        if ($status === 'received') {
            $purchaseOrder->update(['actual_delivery_date' => now()]);
            // Update product stock levels
            foreach ($purchaseOrder->items as $item) {
                $item->product->increment('current_stock', $item->quantity_received);
            }
        }
        
        session()->flash('success', 'Purchase order status updated successfully.');
    }

    public function deletePurchaseOrder($purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);
        
        if ($purchaseOrder->status !== 'draft') {
            session()->flash('error', 'Only draft purchase orders can be deleted.');
            return;
        }
        
        $purchaseOrder->delete();
        session()->flash('success', 'Purchase order deleted successfully.');
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'createdBy', 'items.product'])
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('supplier', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->supplierFilter, function ($query) {
                $query->where('supplier_id', $this->supplierFilter);
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('order_date', today());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'this_month') {
                    $query->whereMonth('order_date', now()->month)
                          ->whereYear('order_date', now()->year);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }
}
