<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class Edit extends Component
{
    public Product $product;

    public $name = '';
    public $sku = '';
    public $description = '';
    public $category_id = '';
    public $cost_price = '';
    public $selling_price = '';
    public $current_stock = '';
    public $minimum_stock = '';
    public $supplier = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100',
        'description' => 'nullable|string|max:1000',
        'category_id' => 'required|exists:categories,id',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'current_stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'supplier' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        
        // Load existing product data
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->current_stock = $product->current_stock;
        $this->minimum_stock = $product->minimum_stock;
        $this->supplier = $product->supplier;
        $this->is_active = $product->is_active;
    }

    public function save()
    {
        // Update SKU validation rule to exclude current product
        $this->rules['sku'] = 'required|string|max:100|unique:products,sku,' . $this->product->id;
        
        $this->validate();

        $this->product->update([
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'current_stock' => $this->current_stock,
            'minimum_stock' => $this->minimum_stock,
            'supplier' => $this->supplier,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Product updated successfully!');
        return redirect()->route('admin.inventory.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.inventory.edit', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
