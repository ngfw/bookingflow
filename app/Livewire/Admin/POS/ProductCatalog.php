<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $supplierFilter = '';
    public $priceRange = '';
    public $stockFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showInactive = false;

    public $selectedProduct = null;
    public $showProductModal = false;

    public function mount()
    {
        // Initialize component
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSupplierFilter()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function updatedShowInactive()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function viewProduct($productId)
    {
        $this->selectedProduct = Product::with(['category', 'supplier'])->find($productId);
        $this->showProductModal = true;
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->selectedProduct = null;
    }

    public function toggleProductStatus($productId)
    {
        $product = Product::find($productId);
        $product->update(['is_active' => !$product->is_active]);
        
        session()->flash('success', 'Product status updated successfully.');
    }

    public function getProducts()
    {
        $query = Product::with(['category', 'supplier'])
            ->where('type', 'retail');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        // Apply supplier filter
        if ($this->supplierFilter) {
            $query->where('supplier_id', $this->supplierFilter);
        }

        // Apply price range filter
        if ($this->priceRange) {
            switch ($this->priceRange) {
                case 'under_10':
                    $query->where('retail_price', '<', 10);
                    break;
                case '10_25':
                    $query->whereBetween('retail_price', [10, 25]);
                    break;
                case '25_50':
                    $query->whereBetween('retail_price', [25, 50]);
                    break;
                case '50_100':
                    $query->whereBetween('retail_price', [50, 100]);
                    break;
                case 'over_100':
                    $query->where('retail_price', '>', 100);
                    break;
            }
        }

        // Apply stock filter
        if ($this->stockFilter) {
            switch ($this->stockFilter) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'minimum_stock');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '=', 0);
                    break;
            }
        }

        // Apply active/inactive filter
        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getCategories()
    {
        return Category::orderBy('name')->get();
    }

    public function getSuppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    public function getStats()
    {
        $totalProducts = Product::where('type', 'retail')->count();
        $activeProducts = Product::where('type', 'retail')->where('is_active', true)->count();
        $lowStockProducts = Product::where('type', 'retail')
            ->whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->count();
        $outOfStockProducts = Product::where('type', 'retail')
            ->where('stock_quantity', 0)
            ->where('is_active', true)
            ->count();

        return [
            'total' => $totalProducts,
            'active' => $activeProducts,
            'low_stock' => $lowStockProducts,
            'out_of_stock' => $outOfStockProducts,
        ];
    }

    public function render()
    {
        $products = $this->getProducts();
        $categories = $this->getCategories();
        $suppliers = $this->getSuppliers();
        $stats = $this->getStats();

        return view('livewire.admin.pos.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}
