<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Point of Sale</h1>
                <p class="text-gray-600">Quick sales and retail transactions</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.pos.catalog') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Product Catalog
                </a>
                <a href="{{ route('admin.pos.promotions') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Promotions
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Search & Selection -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Products</h3>
                
                <!-- Product Search -->
                <div class="mb-4">
                    <input 
                        type="text" 
                        wire:model.live="searchProduct" 
                        placeholder="Search products by name, SKU, or description..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                    @forelse($products as $product)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                             wire:click="addToCart({{ $product->id }})">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                <span class="text-sm text-gray-500">{{ $product->sku }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($product->description, 50) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-green-600">${{ number_format($product->retail_price, 2) }}</span>
                                <span class="text-sm text-gray-500">Stock: {{ $product->stock_quantity }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            <p>No products found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Cart & Checkout -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cart</h3>

                <!-- Client Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client (Optional)</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live="searchClient" 
                            placeholder="Search clients..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                        @if($selectedClient)
                            <button wire:click="clearClient" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                    
                    @if($selectedClient)
                        <div class="mt-2 p-2 bg-blue-50 rounded-md">
                            <p class="text-sm font-medium text-blue-900">{{ $selectedClient->first_name }} {{ $selectedClient->last_name }}</p>
                            <p class="text-xs text-blue-600">{{ $selectedClient->email }}</p>
                        </div>
                    @endif

                    @if(count($clients) > 0)
                        <div class="mt-2 border rounded-md max-h-32 overflow-y-auto">
                            @foreach($clients as $client)
                                <div class="p-2 hover:bg-gray-50 cursor-pointer" wire:click="selectClient({{ $client->id }})">
                                    <p class="text-sm font-medium">{{ $client->first_name }} {{ $client->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->email }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Cart Items -->
                <div class="mb-4 max-h-64 overflow-y-auto">
                    @forelse($cart as $productId => $item)
                        <div class="flex items-center justify-between py-2 border-b">
                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $item['sku'] }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="number" 
                                    wire:change="updateCartQuantity({{ $productId }}, $event.target.value)"
                                    value="{{ $item['quantity'] }}"
                                    min="1"
                                    max="{{ $item['stock'] }}"
                                    class="w-16 text-center border-gray-300 rounded text-sm"
                                >
                                <span class="text-sm font-medium w-16 text-right">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                <button wire:click="removeFromCart({{ $productId }})" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Cart is empty</p>
                    @endforelse
                </div>

                <!-- Promo Code -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            wire:model="promoCode" 
                            placeholder="Enter promo code..."
                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                        <button 
                            wire:click="applyPromoCode" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                        >
                            Apply
                        </button>
                    </div>
                </div>

                <!-- Applied Promotions -->
                @if(count($appliedPromotions) > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Applied Promotions</label>
                        <div class="space-y-2">
                            @foreach($appliedPromotions as $appliedPromotion)
                                <div class="flex items-center justify-between p-2 bg-green-50 rounded-md">
                                    <div>
                                        <p class="text-sm font-medium text-green-900">{{ $appliedPromotion['promotion']->name }}</p>
                                        <p class="text-xs text-green-600">{{ ucfirst(str_replace('_', ' ', $appliedPromotion['promotion']->type)) }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-semibold text-green-700">-${{ number_format($appliedPromotion['discount'], 2) }}</span>
                                        <button 
                                            wire:click="removePromotion({{ $appliedPromotion['promotion']->id }})" 
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Manual Discount -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Manual Discount</label>
                    <div class="flex space-x-2">
                        <select wire:model.live="discountType" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="percentage">%</option>
                            <option value="fixed">$</option>
                        </select>
                        <input 
                            type="number" 
                            wire:model.live="discountValue" 
                            placeholder="0"
                            step="0.01"
                            min="0"
                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Totals -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Subtotal:</span>
                        <span class="text-sm font-medium">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($this->getTotalPromotionDiscount() > 0)
                        <div class="flex justify-between text-green-600">
                            <span class="text-sm">Promotion Discount:</span>
                            <span class="text-sm font-medium">-${{ number_format($this->getTotalPromotionDiscount(), 2) }}</span>
                        </div>
                    @endif
                    
                    @if($discountValue > 0)
                        <div class="flex justify-between text-blue-600">
                            <span class="text-sm">Manual Discount:</span>
                            <span class="text-sm font-medium">-${{ number_format($discountType === 'percentage' ? ($subtotal * $discountValue) / 100 : $discountValue, 2) }}</span>
                        </div>
                    @endif
                    
                    @if($discountAmount > 0)
                        <div class="flex justify-between text-green-600">
                            <span class="text-sm">Total Discount:</span>
                            <span class="text-sm font-medium">-${{ number_format($discountAmount, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tax ({{ number_format($taxRate * 100, 1) }}%):</span>
                        <span class="text-sm font-medium">${{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-semibold">Total:</span>
                        <span class="font-semibold text-lg">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <!-- Payment -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select wire:model.live="paymentMethod" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="digital">Digital</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                    <input 
                        type="number" 
                        wire:model.live="amountPaid" 
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                @if($change > 0)
                    <div class="mb-4 p-3 bg-green-50 rounded-md">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-green-900">Change:</span>
                            <span class="text-sm font-semibold text-green-900">${{ number_format($change, 2) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="space-y-2">
                    <button 
                        wire:click="processSale" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-medium disabled:opacity-50"
                        @if(empty($cart) || $total <= 0 || $amountPaid < $total) disabled @endif
                    >
                        Complete Sale
                    </button>
                    <button 
                        wire:click="clearCart" 
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md font-medium"
                        @if(empty($cart)) disabled @endif
                    >
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($showReceipt && $currentSale)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Receipt</h3>
                        
                        <div class="text-left space-y-2 mb-4">
                            <p class="text-sm"><strong>Invoice #:</strong> {{ $currentSale->invoice_number }}</p>
                            <p class="text-sm"><strong>Date:</strong> {{ $currentSale->invoice_date->format('M d, Y H:i') }}</p>
                            @if($currentSale->client)
                                <p class="text-sm"><strong>Client:</strong> {{ $currentSale->client->first_name }} {{ $currentSale->client->last_name }}</p>
                            @endif
                            <p class="text-sm"><strong>Cashier:</strong> {{ auth()->user()->name }}</p>
                        </div>

                        <div class="border-t pt-2 mb-4">
                            @foreach($currentSale->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                    <span>${{ number_format($item->total_price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-2 space-y-1">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span>${{ number_format($currentSale->subtotal, 2) }}</span>
                            </div>
                            @if($currentSale->discount_amount > 0)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($currentSale->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span>Tax:</span>
                                <span>${{ number_format($currentSale->tax_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-semibold border-t pt-1">
                                <span>Total:</span>
                                <span>${{ number_format($currentSale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2 mt-6">
                        <button 
                            wire:click="printReceipt" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium"
                        >
                            Print Receipt
                        </button>
                        <button 
                            wire:click="closeReceipt" 
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md font-medium"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
