<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Mobile POS</h1>
                    <p class="text-sm text-gray-600">Quick sales & transactions</p>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="toggleView('products')" 
                            class="px-3 py-1 rounded-lg text-sm {{ $currentView === 'products' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        Products
                    </button>
                    <button wire:click="toggleView('cart')" 
                            class="px-3 py-1 rounded-lg text-sm {{ $currentView === 'cart' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        Cart ({{ count($cart) }})
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="px-4 py-6">
        @if($currentView === 'products')
            <!-- Products View -->
            <div class="space-y-4">
                <!-- Search & Filter -->
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="mb-4">
                        <input wire:model.live="searchProduct" type="text" 
                               placeholder="Search products..." 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="flex space-x-2 overflow-x-auto pb-2">
                        <button wire:click="$set('selectedCategory', '')" 
                                class="flex-shrink-0 px-4 py-2 rounded-full {{ !$selectedCategory ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                            All
                        </button>
                        @foreach(\App\Models\Category::all() as $category)
                            <button wire:click="$set('selectedCategory', '{{ $category->id }}')" 
                                    class="flex-shrink-0 px-4 py-2 rounded-full {{ $selectedCategory == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 gap-3">
                    @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})" 
                             class="bg-white rounded-xl shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-600 mb-2">{{ Str::limit($product->description, 30) }}</p>
                                <div class="text-lg font-bold text-green-600">${{ number_format($product->retail_price, 2) }}</div>
                                <div class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white rounded-xl shadow-sm p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-gray-500">No products found</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @elseif($currentView === 'cart')
            <!-- Cart View -->
            <div class="space-y-4">
                <!-- Client Selection -->
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Client</h3>
                        <button wire:click="toggleClientSearch" class="text-blue-600 text-sm font-medium">
                            {{ $showClientSearch ? 'Cancel' : 'Search' }}
                        </button>
                    </div>
                    
                    @if($selectedClient)
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="font-medium text-blue-900">{{ $selectedClient->first_name }} {{ $selectedClient->last_name }}</p>
                            <p class="text-sm text-blue-600">{{ $selectedClient->email }}</p>
                            <button wire:click="clearClient" class="text-red-600 text-sm mt-1">Remove</button>
                        </div>
                    @elseif($showClientSearch)
                        <div class="space-y-3">
                            <input wire:model.live="searchClient" type="text" 
                                   placeholder="Search clients..." 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            
                            @if(count($clients) > 0)
                                <div class="max-h-32 overflow-y-auto">
                                    @foreach($clients as $client)
                                        <button wire:click="selectClient({{ $client->id }})" 
                                                class="w-full text-left p-3 hover:bg-gray-50 rounded-lg">
                                            <p class="font-medium">{{ $client->first_name }} {{ $client->last_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No client selected</p>
                    @endif
                </div>

                <!-- Cart Items -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Cart Items</h3>
                    </div>
                    
                    @if(count($cart) > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($cart as $productId => $item)
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item['sku'] }}</p>
                                            <p class="text-sm font-semibold text-green-600">${{ number_format($item['price'], 2) }}</p>
                                        </div>
                                        
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center space-x-2">
                                                <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})" 
                                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                                <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})" 
                                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                                <button wire:click="removeFromCart({{ $productId }})" 
                                                        class="text-red-600 text-sm">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <p class="text-gray-500">Cart is empty</p>
                        </div>
                    @endif
                </div>

                <!-- Promo Code -->
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">Promo Code</h3>
                        <button wire:click="togglePromoCode" class="text-blue-600 text-sm font-medium">
                            {{ $showPromoCode ? 'Cancel' : 'Apply' }}
                        </button>
                    </div>
                    
                    @if($showPromoCode)
                        <div class="flex space-x-2">
                            <input wire:model="promoCode" type="text" 
                                   placeholder="Enter promo code..." 
                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            <button wire:click="applyPromoCode" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium">
                                Apply
                            </button>
                        </div>
                    @endif
                    
                    @if(count($appliedPromotions) > 0)
                        <div class="mt-3 space-y-2">
                            @foreach($appliedPromotions as $appliedPromotion)
                                <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-green-900">{{ $appliedPromotion['promotion']->name }}</p>
                                        <p class="text-xs text-green-600">-${{ number_format($appliedPromotion['discount'], 2) }}</p>
                                    </div>
                                    <button wire:click="removePromotion({{ $appliedPromotion['promotion']->id }})" 
                                            class="text-red-600 text-sm">Remove</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Order Summary -->
                @if(count($cart) > 0)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($discountAmount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Discount:</span>
                                    <span class="font-medium">-${{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">${{ number_format($taxAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span class="text-green-600">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <button wire:click="togglePaymentModal" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold text-lg mt-4">
                            Process Payment
                        </button>
                    </div>
                @endif
            </div>

        @elseif($currentView === 'receipt')
            <!-- Receipt View -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                    <p class="text-gray-600">Transaction completed</p>
                </div>

                @if($currentSale)
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Receipt</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Invoice #:</span>
                                <span class="font-medium">{{ $currentSale->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium">{{ $currentSale->invoice_date->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($currentSale->client)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Client:</span>
                                    <span class="font-medium">{{ $currentSale->client->first_name }} {{ $currentSale->client->last_name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-medium">${{ number_format($currentSale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-3">
                    <button wire:click="newSale" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium">
                        New Sale
                    </button>
                    <button wire:click="toggleView('products')" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium">
                        Back to Products
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
            <div class="bg-white rounded-t-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Payment</h3>
                        <button wire:click="togglePaymentModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="setPaymentMethod('cash')" 
                                        class="py-3 px-4 rounded-lg border-2 {{ $paymentMethod === 'cash' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    Cash
                                </button>
                                <button wire:click="setPaymentMethod('card')" 
                                        class="py-3 px-4 rounded-lg border-2 {{ $paymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                    Card
                                </button>
                            </div>
                        </div>

                        <!-- Amount Paid -->
                        @if($paymentMethod === 'cash')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                                <input wire:model.live="amountPaid" type="number" step="0.01" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                
                                <!-- Quick Amount Buttons -->
                                <div class="grid grid-cols-3 gap-2 mt-3">
                                    @foreach($quickAmounts as $amount)
                                        <button wire:click="setQuickAmount({{ $amount }})" 
                                                class="py-2 px-3 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm">
                                            ${{ $amount }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Change -->
                        @if($paymentMethod === 'cash' && $amountPaid > $total)
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="flex justify-between">
                                    <span class="text-green-800">Change:</span>
                                    <span class="font-semibold text-green-900">${{ number_format($change, 2) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Total -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-green-600">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Process Payment Button -->
                        <button wire:click="processPayment" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold text-lg">
                            Process Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 left-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 left-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>

