<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class Create extends Component
{
    public $name = '';
    public $sku = '';
    public $description = '';
    public $category_id = '';
    public $supplier_id = '';
    public $cost_price = '';
    public $selling_price = '';
    public $current_stock = '';
    public $minimum_stock = '';
    public $supplier = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100|unique:products,sku',
        'description' => 'nullable|string|max:1000',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'current_stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'supplier' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        Product::create([
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'current_stock' => $this->current_stock,
            'minimum_stock' => $this->minimum_stock,
            'supplier' => $this->supplier,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Product created successfully!');
        return redirect()->route('admin.inventory.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.inventory.create', [
            'categories' => $categories,
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }
}
