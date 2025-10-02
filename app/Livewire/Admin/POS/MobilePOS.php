<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\InvoiceItem;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobilePOS extends Component
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
    public $appliedPromotions = [];
    public $promoCode = '';
    public $availablePromotions = [];

    // Mobile-specific properties
    public $currentView = 'products'; // products, cart, payment, receipt
    public $showClientSearch = false;
    public $showPromoCode = false;
    public $showPaymentModal = false;
    public $quickAmounts = [5, 10, 20, 50, 100];
    public $selectedCategory = '';

    public function mount()
    {
        $this->loadProducts();
        $this->loadClients();
        $this->loadPromotions();
    }

    public function loadProducts()
    {
        $query = Product::where('is_active', true)
            ->where('type', 'retail'); // Only retail products for POS

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $this->products = $query->orderBy('name')->get();
    }

    public function loadClients()
    {
        if (strlen($this->searchClient) >= 2) {
            $this->clients = Client::where('first_name', 'like', '%' . $this->searchClient . '%')
                ->orWhere('last_name', 'like', '%' . $this->searchClient . '%')
                ->orWhere('email', 'like', '%' . $this->searchClient . '%')
                ->orWhere('phone', 'like', '%' . $this->searchClient . '%')
                ->limit(10)
                ->get();
        } else {
            $this->clients = [];
        }
    }

    public function loadPromotions()
    {
        $this->availablePromotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }

    public function updatedSearchClient()
    {
        $this->loadClients();
    }

    public function updatedSearchProduct()
    {
        $this->loadProducts();
    }

    public function updatedSelectedCategory()
    {
        $this->loadProducts();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->stock_quantity <= 0) {
            session()->flash('error', 'Product is out of stock');
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] >= $product->stock_quantity) {
                session()->flash('error', 'Cannot add more items. Stock limit reached.');
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->retail_price,
                'quantity' => 1,
                'stock' => $product->stock_quantity,
            ];
        }

        $this->calculateTotals();
        session()->flash('success', 'Product added to cart');
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotals();
    }

    public function updateCartQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (isset($this->cart[$productId])) {
            $maxStock = $this->cart[$productId]['stock'];
            if ($quantity > $maxStock) {
                session()->flash('error', "Cannot add more than {$maxStock} items");
                return;
            }
            $this->cart[$productId]['quantity'] = $quantity;
            $this->calculateTotals();
        }
    }

    public function selectClient($clientId)
    {
        $this->selectedClient = Client::find($clientId);
        $this->searchClient = '';
        $this->clients = [];
        $this->showClientSearch = false;
    }

    public function clearClient()
    {
        $this->selectedClient = null;
        $this->searchClient = '';
        $this->clients = [];
    }

    public function applyPromoCode()
    {
        if (empty($this->promoCode)) {
            return;
        }

        $promotion = Promotion::where('promo_code', $this->promoCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promotion) {
            session()->flash('error', 'Invalid promo code');
            return;
        }

        // Check if promotion is already applied
        foreach ($this->appliedPromotions as $appliedPromotion) {
            if ($appliedPromotion['promotion']->id === $promotion->id) {
                session()->flash('error', 'Promotion already applied');
                return;
            }
        }

        $discount = $this->calculatePromotionDiscount($promotion);
        if ($discount > 0) {
            $this->appliedPromotions[] = [
                'promotion' => $promotion,
                'discount' => $discount,
            ];
            $this->promoCode = '';
            $this->calculateTotals();
            session()->flash('success', 'Promotion applied successfully');
        } else {
            session()->flash('error', 'Promotion does not apply to current cart');
        }
    }

    public function removePromotion($promotionId)
    {
        $this->appliedPromotions = array_filter($this->appliedPromotions, function($item) use ($promotionId) {
            return $item['promotion']->id !== $promotionId;
        });
        $this->calculateTotals();
    }

    private function calculatePromotionDiscount($promotion)
    {
        $subtotal = $this->subtotal;
        
        switch ($promotion->type) {
            case 'percentage':
                return $subtotal * ($promotion->discount_value / 100);
            case 'fixed':
                return min($promotion->discount_value, $subtotal);
            case 'buy_x_get_y':
                // Simplified implementation
                return $subtotal * 0.1; // 10% discount
            default:
                return 0;
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        // Apply promotions
        $totalPromotionDiscount = 0;
        foreach ($this->appliedPromotions as $appliedPromotion) {
            $totalPromotionDiscount += $appliedPromotion['discount'];
        }

        // Apply manual discount
        if ($this->discountType === 'percentage') {
            $this->discountAmount = $this->subtotal * ($this->discountValue / 100);
        } else {
            $this->discountAmount = min($this->discountValue, $this->subtotal);
        }

        $discountedSubtotal = $this->subtotal - $totalPromotionDiscount - $this->discountAmount;
        $this->taxAmount = $discountedSubtotal * $this->taxRate;
        $this->total = $discountedSubtotal + $this->taxAmount;
    }

    public function updatedDiscountValue()
    {
        $this->calculateTotals();
    }

    public function updatedDiscountType()
    {
        $this->calculateTotals();
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->amountPaid < $this->total) {
            session()->flash('error', 'Insufficient payment amount');
            return;
        }

        try {
            DB::beginTransaction();

            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $this->selectedClient ? $this->selectedClient->id : null,
                'invoice_number' => 'POS-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
                'invoice_date' => now(),
                'due_date' => now(),
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'discount_amount' => $this->discountAmount,
                'total_amount' => $this->total,
                'status' => 'paid',
                'notes' => 'POS Sale',
            ]);

            // Create invoice items
            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                // Update stock
                $product = Product::find($item['id']);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Create payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_date' => now(),
                'status' => 'completed',
                'reference_number' => 'POS-' . time(),
            ]);

            DB::commit();

            $this->currentSale = $invoice;
            $this->showReceipt = true;
            $this->currentView = 'receipt';
            
            // Reset cart
            $this->cart = [];
            $this->selectedClient = null;
            $this->appliedPromotions = [];
            $this->discountValue = 0;
            $this->amountPaid = 0;
            $this->calculateTotals();

            session()->flash('success', 'Payment processed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function setQuickAmount($amount)
    {
        $this->amountPaid = $amount;
        $this->change = max(0, $this->amountPaid - $this->total);
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = $method;
        if ($method === 'card') {
            $this->amountPaid = $this->total;
            $this->change = 0;
        }
    }

    public function toggleView($view)
    {
        $this->currentView = $view;
    }

    public function toggleClientSearch()
    {
        $this->showClientSearch = !$this->showClientSearch;
    }

    public function togglePromoCode()
    {
        $this->showPromoCode = !$this->showPromoCode;
    }

    public function togglePaymentModal()
    {
        $this->showPaymentModal = !$this->showPaymentModal;
    }

    public function newSale()
    {
        $this->cart = [];
        $this->selectedClient = null;
        $this->appliedPromotions = [];
        $this->discountValue = 0;
        $this->amountPaid = 0;
        $this->change = 0;
        $this->showReceipt = false;
        $this->currentSale = null;
        $this->currentView = 'products';
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.admin.pos.mobile-pos');
    }
}

