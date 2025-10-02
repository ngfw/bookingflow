<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Product Catalog</h1>
                <p class="text-gray-600">Manage retail products and inventory</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    POS Terminal
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['out_of_stock'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Search products..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select wire:model.live="categoryFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Supplier Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                <select wire:model.live="supplierFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                <select wire:model.live="priceRange" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Prices</option>
                    <option value="under_10">Under $10</option>
                    <option value="10_25">$10 - $25</option>
                    <option value="25_50">$25 - $50</option>
                    <option value="50_100">$50 - $100</option>
                    <option value="over_100">Over $100</option>
                </select>
            </div>

            <!-- Stock Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                <select wire:model.live="stockFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Stock</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>

            <!-- Show Inactive -->
            <div class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showInactive" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Show Inactive</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">{{ $products->total() }} products found</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Sort by:</span>
                        <select wire:model.live="sortBy" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="name">Name</option>
                            <option value="retail_price">Price</option>
                            <option value="stock_quantity">Stock</option>
                            <option value="created_at">Date Added</option>
                        </select>
                        <button wire:click="sortBy('{{ $sortBy }}')" class="text-gray-400 hover:text-gray-600">
                            @if($sortDirection === 'asc')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                            <div class="flex items-center space-x-1">
                                @if($product->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactive</span>
                                @endif
                            </div>
                        </div>

                        @if($product->image)
                            <div class="mb-3">
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded">
                            </div>
                        @endif

                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($product->description, 80) }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">SKU:</span>
                                <span class="font-medium">{{ $product->sku }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Price:</span>
                                <span class="font-semibold text-green-600">${{ number_format($product->retail_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Stock:</span>
                                <span class="font-medium @if($product->stock_quantity <= $product->minimum_stock) text-red-600 @elseif($product->stock_quantity == 0) text-red-600 @else text-gray-900 @endif">
                                    {{ $product->stock_quantity }}
                                </span>
                            </div>
                            @if($product->category)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Category:</span>
                                    <span class="font-medium">{{ $product->category->name }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            <button 
                                wire:click="viewProduct({{ $product->id }})" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium"
                            >
                                View Details
                            </button>
                            <button 
                                wire:click="toggleProductStatus({{ $product->id }})" 
                                class="px-3 py-2 border border-gray-300 rounded text-sm font-medium hover:bg-gray-50"
                            >
                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    @if($showProductModal && $selectedProduct)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $selectedProduct->name }}</h3>
                        
                        @if($selectedProduct->image)
                            <div class="mb-4">
                                <img src="{{ Storage::url($selectedProduct->image) }}" alt="{{ $selectedProduct->name }}" class="w-full h-48 object-cover rounded">
                            </div>
                        @endif

                        <div class="text-left space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">SKU:</span>
                                <span class="text-sm">{{ $selectedProduct->sku }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Brand:</span>
                                <span class="text-sm">{{ $selectedProduct->brand ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Price:</span>
                                <span class="text-sm font-semibold text-green-600">${{ number_format($selectedProduct->retail_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Cost:</span>
                                <span class="text-sm">${{ number_format($selectedProduct->cost_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Stock:</span>
                                <span class="text-sm @if($selectedProduct->stock_quantity <= $selectedProduct->minimum_stock) text-red-600 @elseif($selectedProduct->stock_quantity == 0) text-red-600 @else text-gray-900 @endif">
                                    {{ $selectedProduct->stock_quantity }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Min Stock:</span>
                                <span class="text-sm">{{ $selectedProduct->minimum_stock }}</span>
                            </div>
                            @if($selectedProduct->category)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Category:</span>
                                    <span class="text-sm">{{ $selectedProduct->category->name }}</span>
                                </div>
                            @endif
                            @if($selectedProduct->supplier)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Supplier:</span>
                                    <span class="text-sm">{{ $selectedProduct->supplier->name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-600">Status:</span>
                                <span class="text-sm @if($selectedProduct->is_active) text-green-600 @else text-red-600 @endif">
                                    {{ $selectedProduct->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        @if($selectedProduct->description)
                            <div class="text-left mb-4">
                                <p class="text-sm font-medium text-gray-600 mb-1">Description:</p>
                                <p class="text-sm text-gray-900">{{ $selectedProduct->description }}</p>
                            </div>
                        @endif

                        @if($selectedProduct->usage_notes)
                            <div class="text-left mb-4">
                                <p class="text-sm font-medium text-gray-600 mb-1">Usage Notes:</p>
                                <p class="text-sm text-gray-900">{{ $selectedProduct->usage_notes }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex space-x-2 mt-6">
                        <button 
                            wire:click="toggleProductStatus({{ $selectedProduct->id }})" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium"
                        >
                            {{ $selectedProduct->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button 
                            wire:click="closeProductModal" 
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
