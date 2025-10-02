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

class Index extends Component
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

    public function mount()
    {
        $this->loadProducts();
        $this->loadClients();
    }

    public function loadProducts()
    {
        $this->products = Product::where('is_active', true)
            ->where('type', 'retail') // Only retail products for POS
            ->orderBy('name')
            ->get();
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

    public function updatedSearchClient()
    {
        $this->loadClients();
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 2) {
            $this->products = Product::where('is_active', true)
                ->where('type', 'retail')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchProduct . '%')
                          ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
                          ->orWhere('description', 'like', '%' . $this->searchProduct . '%');
                })
                ->orderBy('name')
                ->get();
        } else {
            $this->loadProducts();
        }
    }

    public function selectClient($clientId)
    {
        $this->selectedClient = Client::find($clientId);
        $this->searchClient = $this->selectedClient->first_name . ' ' . $this->selectedClient->last_name;
        $this->clients = [];
    }

    public function clearClient()
    {
        $this->selectedClient = null;
        $this->searchClient = '';
        $this->clients = [];
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->stock_quantity <= 0) {
            session()->flash('error', 'Product not available or out of stock.');
            return;
        }

        $cartKey = $productId;
        
        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] >= $product->stock_quantity) {
                session()->flash('error', 'Not enough stock available.');
                return;
            }
            $this->cart[$cartKey]['quantity']++;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->retail_price,
                'quantity' => 1,
                'sku' => $product->sku,
                'stock' => $product->stock_quantity,
            ];
        }

        $this->updateTotals();
        session()->flash('success', 'Product added to cart.');
    }

    public function updateCartQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$productId]);
        } else {
            $product = Product::find($productId);
            if ($quantity > $product->stock_quantity) {
                session()->flash('error', 'Not enough stock available.');
                return;
            }
            $this->cart[$productId]['quantity'] = $quantity;
        }
        
        $this->updateTotals();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->updateTotals();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->updateTotals();
    }

    public function updateTotals()
    {
        $this->subtotal = 0;
        
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        // Calculate manual discount
        $manualDiscount = 0;
        if ($this->discountType === 'percentage') {
            $manualDiscount = ($this->subtotal * $this->discountValue) / 100;
        } else {
            $manualDiscount = $this->discountValue;
        }

        // Calculate promotion discounts
        $this->calculatePromotionDiscounts();

        // Total discount is manual + promotion discounts
        $this->discountAmount = $manualDiscount + $this->getTotalPromotionDiscount();

        // Calculate tax on discounted amount
        $taxableAmount = $this->subtotal - $this->discountAmount;
        $this->taxAmount = $taxableAmount * $this->taxRate;
        
        $this->total = $taxableAmount + $this->taxAmount;
        
        // Calculate change
        $this->change = max(0, $this->amountPaid - $this->total);
    }

    public function updatedDiscountValue()
    {
        $this->updateTotals();
    }

    public function updatedDiscountType()
    {
        $this->updateTotals();
    }

    public function updatedAmountPaid()
    {
        $this->updateTotals();
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        if ($this->total <= 0) {
            session()->flash('error', 'Total amount must be greater than zero.');
            return;
        }

        if ($this->amountPaid < $this->total) {
            session()->flash('error', 'Amount paid is less than total amount.');
            return;
        }

        try {
            DB::beginTransaction();

            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $this->selectedClient ? $this->selectedClient->id : null,
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => Carbon::now(),
                'due_date' => Carbon::now(),
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'discount_amount' => $this->discountAmount,
                'total_amount' => $this->total,
                'status' => 'paid',
                'notes' => 'POS Sale',
                'created_by' => auth()->id(),
            ]);

            // Create invoice items
            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'service_id' => null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Create payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $this->amountPaid,
                'payment_method' => $this->paymentMethod,
                'payment_date' => Carbon::now(),
                'status' => 'completed',
                'reference_number' => $this->generatePaymentReference(),
                'notes' => 'POS Payment',
            ]);

            DB::commit();

            $this->currentSale = $invoice;
            $this->showReceipt = true;
            
            // Reset form
            $this->cart = [];
            $this->selectedClient = null;
            $this->searchClient = '';
            $this->discountAmount = 0;
            $this->discountValue = 0;
            $this->amountPaid = 0;
            $this->updateTotals();

            session()->flash('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error processing sale: ' . $e->getMessage());
        }
    }

    public function printReceipt()
    {
        // This would integrate with a receipt printer
        // For now, we'll just show a success message
        session()->flash('success', 'Receipt sent to printer.');
    }

    public function closeReceipt()
    {
        $this->showReceipt = false;
        $this->currentSale = null;
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'POS';
        $date = Carbon::now()->format('Ymd');
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . $date . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentReference()
    {
        return 'PAY' . Carbon::now()->format('YmdHis') . rand(100, 999);
    }

    public function calculatePromotionDiscounts()
    {
        $this->appliedPromotions = [];
        
        if (empty($this->cart)) {
            return;
        }

        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->get();

        foreach ($promotions as $promotion) {
            if ($promotion->canBeUsed()) {
                $discount = $promotion->calculateDiscount($this->cart, $this->subtotal);
                
                if ($discount > 0) {
                    $this->appliedPromotions[] = [
                        'promotion' => $promotion,
                        'discount' => $discount,
                    ];
                }
            }
        }
    }

    public function getTotalPromotionDiscount()
    {
        return collect($this->appliedPromotions)->sum('discount');
    }

    public function applyPromoCode()
    {
        if (empty($this->promoCode)) {
            return;
        }

        $promotion = Promotion::where('promo_code', $this->promoCode)
            ->where('requires_promo_code', true)
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->first();

        if ($promotion && $promotion->canBeUsed()) {
            $discount = $promotion->calculateDiscount($this->cart, $this->subtotal);
            
            if ($discount > 0) {
                // Check if promotion is already applied
                $alreadyApplied = collect($this->appliedPromotions)
                    ->contains('promotion.id', $promotion->id);
                
                if (!$alreadyApplied) {
                    $this->appliedPromotions[] = [
                        'promotion' => $promotion,
                        'discount' => $discount,
                    ];
                    $this->updateTotals();
                    session()->flash('success', 'Promo code applied successfully!');
                } else {
                    session()->flash('error', 'This promo code is already applied.');
                }
            } else {
                session()->flash('error', 'Promo code does not apply to current cart.');
            }
        } else {
            session()->flash('error', 'Invalid or expired promo code.');
        }
        
        $this->promoCode = '';
    }

    public function removePromotion($promotionId)
    {
        $this->appliedPromotions = collect($this->appliedPromotions)
            ->reject(function ($item) use ($promotionId) {
                return $item['promotion']->id == $promotionId;
            })
            ->values()
            ->toArray();
        
        $this->updateTotals();
        session()->flash('success', 'Promotion removed.');
    }

    public function render()
    {
        return view('livewire.admin.pos.index')->layout('layouts.admin');
    }
}
