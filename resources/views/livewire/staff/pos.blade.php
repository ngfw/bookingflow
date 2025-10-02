<div class="p-4 lg:p-6" x-data="{ showClientSearch: false, showReceipt: @entangle('showReceipt') }">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Point of Sale</h1>
            <p class="text-gray-600">Process retail sales and manage transactions</p>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Receipt Modal -->
        <div x-show="showReceipt" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    @if($currentSale)
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="text-center">
                                <!-- Logo and Salon Name -->
                                <div class="mb-4">
                                    <x-salon-logo class="h-12 w-auto mx-auto mb-2" />
                                    @php
                                        $salonSettings = \App\Models\SalonSetting::getDefault();
                                    @endphp
                                    <h2 class="text-lg font-bold">{{ $salonSettings->salon_name }}</h2>
                                    @if($salonSettings->salon_address)
                                        <p class="text-sm text-gray-600">{{ $salonSettings->salon_address }}</p>
                                    @endif
                                    @if($salonSettings->salon_phone)
                                        <p class="text-sm text-gray-600">{{ $salonSettings->salon_phone }}</p>
                                    @endif
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Receipt Details -->
                                <div class="text-left space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span>Receipt #:</span>
                                        <span>{{ $currentSale->invoice_number }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span>Date:</span>
                                        <span>{{ $currentSale->created_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                    @if($currentSale->staff)
                                        <div class="flex justify-between text-sm">
                                            <span>Staff:</span>
                                            <span>{{ $currentSale->staff->user->name }}</span>
                                        </div>
                                    @endif
                                    @if($currentSale->client)
                                        <div class="flex justify-between text-sm">
                                            <span>Customer:</span>
                                            <span>{{ $currentSale->client->user->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Items -->
                                <div class="space-y-2">
                                    @foreach($currentSale->items as $item)
                                        <div class="flex justify-between text-sm">
                                            <div class="text-left">
                                                <div>{{ $item->product->name }}</div>
                                                <div class="text-gray-500">{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</div>
                                            </div>
                                            <div>${{ number_format($item->total_price, 2) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Totals -->
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span>${{ number_format($currentSale->subtotal, 2) }}</span>
                                    </div>
                                    @if($currentSale->discount_amount > 0)
                                        <div class="flex justify-between text-green-600">
                                            <span>Discount:</span>
                                            <span>-${{ number_format($currentSale->discount_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span>Tax:</span>
                                        <span>${{ number_format($currentSale->tax_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                                        <span>Total:</span>
                                        <span>${{ number_format($currentSale->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="newSale" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            New Sale
                        </button>
                        <button @click="window.print()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main POS Interface -->
        <div x-show="!showReceipt" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Products Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Search -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input wire:model.live="searchProduct" 
                                   type="text" 
                                   placeholder="Search products by name, SKU, or barcode..." 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Products</h3>
                    
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($products as $product)
                                <div class="border rounded-lg p-3 hover:shadow-md transition-shadow cursor-pointer"
                                     wire:click="addToCart({{ $product->id }})">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded mb-2">
                                    @else
                                        <div class="w-full h-20 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="text-sm">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                        <p class="text-green-600 font-semibold">${{ number_format($product->selling_price, 2) }}</p>
                                        <p class="text-gray-500 text-xs">Stock: {{ $product->current_stock }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No products found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cart & Checkout Section -->
            <div class="space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer</h3>
                    
                    @if($selectedClient)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div>
                                <p class="font-medium">{{ $selectedClient->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $selectedClient->user->email }}</p>
                            </div>
                            <button wire:click="$set('selectedClient', null)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @else
                        <div>
                            <input wire:model.live="searchClient" 
                                   type="text" 
                                   placeholder="Search customer by name or email..." 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                            
                            @if($searchClient && $clients->count() > 0)
                                <div class="mt-2 max-h-40 overflow-y-auto border rounded-lg">
                                    @foreach($clients as $client)
                                        <div wire:click="selectClient({{ $client->id }})" 
                                             class="p-2 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                                            <p class="font-medium">{{ $client->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $client->user->email }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cart</h3>
                    
                    @if(count($cart) > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($cart as $item)
                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-sm">{{ $item['name'] }}</h4>
                                        <p class="text-sm text-gray-500">${{ number_format($item['price'], 2) }} each</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" 
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <span class="text-xs">-</span>
                                        </button>
                                        <span class="w-8 text-center text-sm">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" 
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                            <span class="text-xs">+</span>
                                        </button>
                                        <button wire:click="removeFromCart({{ $item['id'] }})" 
                                                class="ml-2 text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="w-16 text-right">
                                        <span class="font-medium">${{ number_format($item['total'], 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Discount -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Discount</h4>
                            <div class="grid grid-cols-3 gap-2">
                                <select wire:model.live="discountType" class="border-gray-300 rounded text-sm">
                                    <option value="percentage">%</option>
                                    <option value="fixed">$</option>
                                </select>
                                <input wire:model.live="discountValue" 
                                       type="number" 
                                       step="0.01" 
                                       min="0" 
                                       class="border-gray-300 rounded text-sm col-span-2">
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($discountAmount > 0)
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span>Tax ({{ $taxRate * 100 }}%):</span>
                                <span>${{ number_format($taxAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Payment Method</h4>
                            <select wire:model.live="paymentMethod" class="w-full border-gray-300 rounded-lg mb-2">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="check">Check</option>
                            </select>

                            @if($paymentMethod === 'cash')
                                <div class="mb-2">
                                    <label class="block text-sm font-medium mb-1">Amount Paid</label>
                                    <input wire:model.live="amountPaid" 
                                           type="number" 
                                           step="0.01" 
                                           min="0" 
                                           class="w-full border-gray-300 rounded-lg">
                                    @if($change > 0)
                                        <p class="text-sm text-green-600 mt-1">Change: ${{ number_format($change, 2) }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Notes (Optional)</label>
                                <textarea wire:model="notes" 
                                          rows="2" 
                                          class="w-full border-gray-300 rounded-lg text-sm"></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button wire:click="processSale" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg transition-colors">
                                Complete Sale
                            </button>
                            <button wire:click="clearCart" 
                                    class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 rounded-lg transition-colors">
                                Clear Cart
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m4.5-5a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Your cart is empty</p>
                            <p class="text-xs text-gray-400">Click on products to add them to cart</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>