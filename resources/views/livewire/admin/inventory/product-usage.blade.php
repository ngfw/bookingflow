<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Product Usage Tracking</h1>
                <p class="text-gray-600">Track product usage per service and manage inventory consumption</p>
            </div>
            <div class="flex space-x-4">
                <button wire:click="showAddForm" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Record Usage
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Add Usage Form -->
    @if($showAddForm)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Record Product Usage</h2>
                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Product Selection -->
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                            <select wire:model="product_id" id="product_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('product_id') border-red-300 @enderror">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->current_stock }})</option>
                                @endforeach
                            </select>
                            @error('product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Appointment Selection -->
                        <div>
                            <label for="appointment_id" class="block text-sm font-medium text-gray-700 mb-2">Appointment (Optional)</label>
                            <select wire:model="appointment_id" id="appointment_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('appointment_id') border-red-300 @enderror">
                                <option value="">No Appointment</option>
                                @foreach($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">{{ $appointment->client->user->name }} - {{ $appointment->service->name }} ({{ Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y g:i A') }})</option>
                                @endforeach
                            </select>
                            @error('appointment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Staff Selection -->
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">Staff Member *</label>
                            <select wire:model="staff_id" id="staff_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('staff_id') border-red-300 @enderror">
                                <option value="">Select Staff</option>
                                @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                                @endforeach
                            </select>
                            @error('staff_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Quantity Used -->
                        <div>
                            <label for="quantity_used" class="block text-sm font-medium text-gray-700 mb-2">Quantity Used *</label>
                            <input wire:model="quantity_used" type="number" min="1" id="quantity_used" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('quantity_used') border-red-300 @enderror">
                            @error('quantity_used') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Cost Per Unit -->
                        <div>
                            <label for="cost_per_unit" class="block text-sm font-medium text-gray-700 mb-2">Cost Per Unit *</label>
                            <input wire:model="cost_per_unit" type="number" step="0.01" min="0" id="cost_per_unit" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('cost_per_unit') border-red-300 @enderror">
                            @error('cost_per_unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Usage Date -->
                        <div>
                            <label for="usage_date" class="block text-sm font-medium text-gray-700 mb-2">Usage Date *</label>
                            <input wire:model="usage_date" type="date" id="usage_date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('usage_date') border-red-300 @enderror">
                            @error('usage_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror" placeholder="Any additional notes about the product usage..."></textarea>
                        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="hideAddForm" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Record Usage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by product name, SKU, or client name..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Date Filter -->
                <div>
                    <select wire:model.live="dateFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                    </select>
                </div>

                <!-- Product Filter -->
                <div>
                    <select wire:model.live="productFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('usage_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th wire:click="sortBy('quantity_used')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Quantity
                        </th>
                        <th wire:click="sortBy('total_cost')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Total Cost
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($usage as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ Carbon\Carbon::parse($record->usage_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $record->product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $record->product->sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->appointment)
                                    <div class="text-sm text-gray-900">{{ $record->appointment->client->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $record->appointment->service->name }}</div>
                                @else
                                    <span class="text-sm text-gray-400">No appointment</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->staff->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->quantity_used }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($record->total_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="deleteUsage({{ $record->id }})" 
                                        wire:confirm="Are you sure you want to delete this usage record? This will restore the stock."
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No usage records found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No product usage has been recorded yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $usage->links() }}
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