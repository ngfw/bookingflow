<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCatalog extends Component
{
    use WithPagination, WithFileUploads;

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
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingProduct = null;

    // Form fields for product creation/editing
    public $name = '';
    public $sku = '';
    public $description = '';
    public $brand = '';
    public $category_id = '';
    public $supplier_id = '';
    public $retail_price = '';
    public $cost_price = '';
    public $stock_quantity = 0;
    public $minimum_stock = 0;
    public $barcode = '';
    public $unit = 'piece';
    public $weight = '';
    public $dimensions = '';
    public $usage_notes = '';
    public $is_active = true;
    public $image = null;
    public $newImage = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100|unique:products,sku',
        'description' => 'nullable|string|max:1000',
        'brand' => 'nullable|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'retail_price' => 'required|numeric|min:0',
        'cost_price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'barcode' => 'nullable|string|max:255',
        'unit' => 'required|string|max:50',
        'weight' => 'nullable|numeric|min:0',
        'dimensions' => 'nullable|string|max:255',
        'usage_notes' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
        'newImage' => 'nullable|image|max:2048',
    ];

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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($productId)
    {
        $this->editingProduct = Product::findOrFail($productId);
        $this->loadProductData($this->editingProduct);
        $this->showEditModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingProduct = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->sku = '';
        $this->description = '';
        $this->brand = '';
        $this->category_id = '';
        $this->supplier_id = '';
        $this->retail_price = '';
        $this->cost_price = '';
        $this->stock_quantity = 0;
        $this->minimum_stock = 0;
        $this->barcode = '';
        $this->unit = 'piece';
        $this->weight = '';
        $this->dimensions = '';
        $this->usage_notes = '';
        $this->is_active = true;
        $this->image = null;
        $this->newImage = null;
        $this->resetErrorBag();
    }

    private function loadProductData(Product $product)
    {
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->brand = $product->brand;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->retail_price = $product->retail_price;
        $this->cost_price = $product->cost_price;
        $this->stock_quantity = $product->stock_quantity;
        $this->minimum_stock = $product->minimum_stock;
        $this->barcode = $product->barcode;
        $this->unit = $product->unit;
        $this->weight = $product->weight;
        $this->dimensions = $product->dimensions;
        $this->usage_notes = $product->usage_notes;
        $this->is_active = $product->is_active;
        $this->image = $product->image;
    }

    public function generateSKU()
    {
        $prefix = 'PRD';
        $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $this->sku = $prefix . $randomNumber;
    }

    public function createProduct()
    {
        $this->validate();

        $imagePath = null;
        if ($this->newImage) {
            $imagePath = $this->newImage->store('products', 'public');
        }

        Product::create([
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'brand' => $this->brand,
            'category_id' => $this->category_id ?: null,
            'supplier_id' => $this->supplier_id ?: null,
            'retail_price' => $this->retail_price,
            'cost_price' => $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'minimum_stock' => $this->minimum_stock,
            'barcode' => $this->barcode,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'usage_notes' => $this->usage_notes,
            'is_active' => $this->is_active,
            'image' => $imagePath,
            'type' => 'retail',
        ]);

        session()->flash('success', 'Product created successfully!');
        $this->closeCreateModal();
    }

    public function updateProduct()
    {
        $this->validate($this->getRulesForUpdate());

        $imagePath = $this->image;
        if ($this->newImage) {
            if ($this->image) {
                Storage::disk('public')->delete($this->image);
            }
            $imagePath = $this->newImage->store('products', 'public');
        }

        $this->editingProduct->update([
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'brand' => $this->brand,
            'category_id' => $this->category_id ?: null,
            'supplier_id' => $this->supplier_id ?: null,
            'retail_price' => $this->retail_price,
            'cost_price' => $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'minimum_stock' => $this->minimum_stock,
            'barcode' => $this->barcode,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'usage_notes' => $this->usage_notes,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        session()->flash('success', 'Product updated successfully!');
        $this->closeEditModal();
    }

    private function getRulesForUpdate()
    {
        $rules = $this->rules;
        $rules['sku'] = 'required|string|max:100|unique:products,sku,' . $this->editingProduct->id;
        return $rules;
    }

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function adjustStock($productId, $adjustment, $reason = 'Manual adjustment')
    {
        $product = Product::findOrFail($productId);
        $newStock = max(0, $product->stock_quantity + $adjustment);
        
        $product->update(['stock_quantity' => $newStock]);
        
        session()->flash('success', "Stock adjusted. New quantity: {$newStock}");
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
