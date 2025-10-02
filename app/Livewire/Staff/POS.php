<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\InvoiceItem;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.staff')]
class POS extends Component
{
    public $cart = [];
    public $selectedClient = null;
    public $searchClient = '';
    public $searchProduct = '';
    public $products = [];
    public $clients = [];
    public $subtotal = 0;
    public $taxRate = 0.08; // 8% tax rate
    public $taxAmount = 0;
    public $discountAmount = 0;
    public $discountType = 'percentage'; // percentage or fixed
    public $discountValue = 0;
    public $total = 0;
    public $paymentMethod = 'cash';
    public $amountPaid = 0;
    public $change = 0;
    public $showReceipt = false;
    public $currentSale = null;
    public $staff;
    public $notes = '';

    public function mount()
    {
        $this->staff = Staff::where('user_id', auth()->id())->first();
        $this->loadProducts();
        $this->loadClients();
    }

    public function loadProducts()
    {
        $query = Product::where('is_active', true)
            ->where('is_for_sale', true) // Only products marked for sale
            ->where('current_stock', '>', 0) // Only products in stock
            ->orderBy('name');

        if ($this->searchProduct) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('barcode', 'like', '%' . $this->searchProduct . '%');
            });
        }

        $this->products = $query->get();
    }

    public function loadClients()
    {
        $query = Client::with('user')->orderBy('created_at', 'desc');

        if ($this->searchClient) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->searchClient . '%')
                  ->orWhere('email', 'like', '%' . $this->searchClient . '%');
            });
        }

        $this->clients = $query->limit(10)->get();
    }

    public function updatedSearchProduct()
    {
        $this->loadProducts();
    }

    public function updatedSearchClient()
    {
        $this->loadClients();
    }

    public function selectClient($clientId)
    {
        $this->selectedClient = Client::with('user')->find($clientId);
        $this->searchClient = '';
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->current_stock <= 0) {
            session()->flash('error', 'Product not available or out of stock.');
            return;
        }

        $existingItem = collect($this->cart)->firstWhere('id', $productId);
        
        if ($existingItem) {
            // Check if we can add more
            $currentQty = $existingItem['quantity'];
            if ($currentQty >= $product->current_stock) {
                session()->flash('error', 'Cannot add more. Insufficient stock.');
                return;
            }
            
            // Update quantity
            $this->cart = collect($this->cart)->map(function($item) use ($productId) {
                if ($item['id'] == $productId) {
                    $item['quantity']++;
                    $item['total'] = $item['price'] * $item['quantity'];
                }
                return $item;
            })->toArray();
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->selling_price,
                'quantity' => 1,
                'total' => $product->selling_price,
                'available_stock' => $product->current_stock
            ];
        }

        $this->calculateTotals();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::find($productId);
        if ($quantity > $product->current_stock) {
            session()->flash('error', 'Cannot exceed available stock.');
            return;
        }

        $this->cart = collect($this->cart)->map(function($item) use ($productId, $quantity) {
            if ($item['id'] == $productId) {
                $item['quantity'] = $quantity;
                $item['total'] = $item['price'] * $quantity;
            }
            return $item;
        })->toArray();

        $this->calculateTotals();
    }

    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->reject(function($item) use ($productId) {
            return $item['id'] == $productId;
        })->values()->toArray();

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum('total');
        
        // Apply discount
        if ($this->discountType === 'percentage') {
            $this->discountAmount = ($this->subtotal * $this->discountValue) / 100;
        } else {
            $this->discountAmount = $this->discountValue;
        }

        $discountedAmount = $this->subtotal - $this->discountAmount;
        $this->taxAmount = $discountedAmount * $this->taxRate;
        $this->total = $discountedAmount + $this->taxAmount;

        // Calculate change
        $this->change = max(0, $this->amountPaid - $this->total);
    }

    public function updatedDiscountValue()
    {
        $this->calculateTotals();
    }

    public function updatedAmountPaid()
    {
        $this->calculateTotals();
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->amountPaid < $this->total) {
            session()->flash('error', 'Insufficient payment amount.');
            return;
        }

        try {
            DB::transaction(function () {
                // Create invoice
                $invoice = Invoice::create([
                    'client_id' => $this->selectedClient?->id,
                    'staff_id' => $this->staff?->id,
                    'invoice_number' => 'POS-' . date('Ymd') . '-' . str_pad(Invoice::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT),
                    'subtotal' => $this->subtotal,
                    'tax_amount' => $this->taxAmount,
                    'discount_amount' => $this->discountAmount,
                    'total_amount' => $this->total,
                    'status' => 'paid',
                    'notes' => $this->notes,
                    'invoice_date' => now(),
                    'due_date' => now(),
                ]);

                // Add invoice items and update stock
                foreach ($this->cart as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'total_price' => $item['total'],
                    ]);

                    // Update product stock
                    $product = Product::find($item['id']);
                    $product->decrement('current_stock', $item['quantity']);
                }

                // Create payment record
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $this->selectedClient?->id,
                    'amount' => $this->total,
                    'payment_method' => $this->paymentMethod,
                    'payment_date' => now(),
                    'status' => 'completed',
                    'notes' => $this->paymentMethod === 'cash' ? "Cash payment. Change: $" . number_format($this->change, 2) : null,
                ]);

                $this->currentSale = $invoice->load(['items.product', 'client.user', 'staff.user']);
                $this->showReceipt = true;
            });

            session()->flash('success', 'Sale completed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing sale: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->selectedClient = null;
        $this->discountValue = 0;
        $this->discountAmount = 0;
        $this->amountPaid = 0;
        $this->notes = '';
        $this->calculateTotals();
    }

    public function newSale()
    {
        $this->clearCart();
        $this->showReceipt = false;
        $this->currentSale = null;
    }

    public function render()
    {
        return view('livewire.staff.pos');
    }
}