<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use App\Models\Product;

class BarcodeScanner extends Component
{
    public $scannedCode = '';
    public $product = null;
    public $scanHistory = [];
    public $showScanner = false;
    public $manualEntry = '';

    public function mount()
    {
        // Load recent scan history from session
        $this->scanHistory = session('barcode_scan_history', []);
    }

    public function scanBarcode($code = null)
    {
        $code = $code ?: $this->scannedCode ?: $this->manualEntry;
        
        if (empty($code)) {
            session()->flash('error', 'Please enter or scan a barcode.');
            return;
        }

        // Look for product by SKU or barcode
        $this->product = Product::where('sku', $code)
            ->orWhere('barcode', $code)
            ->first();

        if ($this->product) {
            // Add to scan history
            $this->addToHistory($this->product, $code);
            session()->flash('success', "Product found: {$this->product->name}");
        } else {
            session()->flash('error', 'Product not found for barcode: ' . $code);
        }

        $this->scannedCode = '';
        $this->manualEntry = '';
    }

    public function manualEntry()
    {
        $this->scanBarcode($this->manualEntry);
    }

    public function toggleScanner()
    {
        $this->showScanner = !$this->showScanner;
    }

    public function updateStock($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        $product->increment('current_stock', $quantity);
        
        session()->flash('success', "Stock updated for {$product->name}. New stock: {$product->fresh()->current_stock}");
        
        // Refresh the product data
        $this->product = $product->fresh();
    }

    public function reduceStock($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->current_stock < $quantity) {
            session()->flash('error', "Insufficient stock. Available: {$product->current_stock}");
            return;
        }
        
        $product->decrement('current_stock', $quantity);
        
        session()->flash('success', "Stock reduced for {$product->name}. New stock: {$product->fresh()->current_stock}");
        
        // Refresh the product data
        $this->product = $product->fresh();
    }

    public function clearHistory()
    {
        $this->scanHistory = [];
        session()->forget('barcode_scan_history');
        session()->flash('success', 'Scan history cleared.');
    }

    public function removeFromHistory($index)
    {
        unset($this->scanHistory[$index]);
        $this->scanHistory = array_values($this->scanHistory);
        session(['barcode_scan_history' => $this->scanHistory]);
    }

    private function addToHistory($product, $code)
    {
        $historyItem = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'barcode' => $code,
            'current_stock' => $product->current_stock,
            'scanned_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Add to beginning of array
        array_unshift($this->scanHistory, $historyItem);
        
        // Keep only last 20 items
        $this->scanHistory = array_slice($this->scanHistory, 0, 20);
        
        // Save to session
        session(['barcode_scan_history' => $this->scanHistory]);
    }

    public function render()
    {
        return view('livewire.admin.inventory.barcode-scanner')->layout('layouts.admin');
    }
}
