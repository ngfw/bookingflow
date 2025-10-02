<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Barcode Scanner</h1>
                <p class="text-gray-600">Scan barcodes to manage inventory quickly</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Scanner Interface -->
        <div class="space-y-6">
            <!-- Scanner Controls -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Scanner Interface</h2>
                
                <!-- Manual Entry -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Manual Barcode Entry</label>
                    <div class="flex space-x-2">
                        <input wire:model="manualEntry" type="text" placeholder="Enter barcode or SKU..." 
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               wire:keydown.enter="manualEntry">
                        <button wire:click="manualEntry" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Scan
                        </button>
                    </div>
                </div>

                <!-- Scanner Toggle -->
                <div class="mb-4">
                    <button wire:click="toggleScanner" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $showScanner ? 'Hide Scanner' : 'Show Scanner' }}
                    </button>
                </div>

                <!-- Scanner Instructions -->
                @if($showScanner)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Scanner Instructions</h3>
                                <div class="mt-1 text-sm text-blue-700">
                                    <p>1. Position the barcode in front of your device camera</p>
                                    <p>2. Ensure good lighting and clear visibility</p>
                                    <p>3. The scanner will automatically detect and process the barcode</p>
                                    <p>4. Or use manual entry for SKU codes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            @if($product)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Product Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Product Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SKU</label>
                                <p class="text-gray-900">{{ $product->sku }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Stock</label>
                                <p class="text-gray-900 font-semibold">{{ $product->current_stock }} units</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cost Price</label>
                                <p class="text-gray-900">${{ number_format($product->cost_price, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                                <p class="text-gray-900">${{ number_format($product->selling_price, 2) }}</p>
                            </div>
                        </div>

                        @if($product->category)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <p class="text-gray-900">{{ $product->category->name }}</p>
                            </div>
                        @endif

                        @if($product->supplier)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                <p class="text-gray-900">{{ $product->supplier->name }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Stock Management -->
                    <div class="mt-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stock Management</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add Stock</label>
                                <div class="flex space-x-2">
                                    <input type="number" id="addQuantity" min="1" value="1" 
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button onclick="addStock({{ $product->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                        Add
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reduce Stock</label>
                                <div class="flex space-x-2">
                                    <input type="number" id="reduceQuantity" min="1" value="1" 
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button onclick="reduceStock({{ $product->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                                        Reduce
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Product Scanned</h3>
                        <p class="mt-1 text-sm text-gray-500">Scan a barcode or enter a SKU to view product information.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Scan History -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Scan History</h2>
                        <button wire:click="clearHistory" class="text-red-600 hover:text-red-800 text-sm">
                            Clear History
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    @if(count($scanHistory) > 0)
                        <div class="space-y-3">
                            @foreach($scanHistory as $index => $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $item['product_name'] }}</p>
                                        <p class="text-sm text-gray-500">SKU: {{ $item['sku'] }} | Stock: {{ $item['current_stock'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $item['scanned_at'] }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button wire:click="scanBarcode('{{ $item['sku'] }}')" 
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            Rescan
                                        </button>
                                        <button wire:click="removeFromHistory({{ $index }})" 
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Scan History</h3>
                            <p class="mt-1 text-sm text-gray-500">Start scanning products to build your history.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>

<script>
function addStock(productId) {
    const quantity = document.getElementById('addQuantity').value;
    if (quantity && quantity > 0) {
        @this.call('updateStock', productId, parseInt(quantity));
    }
}

function reduceStock(productId) {
    const quantity = document.getElementById('reduceQuantity').value;
    if (quantity && quantity > 0) {
        @this.call('reduceStock', productId, parseInt(quantity));
    }
}
</script>