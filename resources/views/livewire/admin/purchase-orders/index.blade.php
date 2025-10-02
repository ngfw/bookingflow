<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Purchase Orders</h1>
                <p class="text-gray-600">Manage purchase orders and inventory procurement</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.purchase-orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Purchase Order
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by order number or supplier..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="ordered">Ordered</option>
                        <option value="received">Received</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Supplier Filter -->
                <div>
                    <select wire:model.live="supplierFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="mt-4">
                <div class="flex space-x-4">
                    <button wire:click="$set('dateFilter', '')" 
                            class="px-3 py-1 text-sm rounded {{ $dateFilter === '' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                        All Time
                    </button>
                    <button wire:click="$set('dateFilter', 'today')" 
                            class="px-3 py-1 text-sm rounded {{ $dateFilter === 'today' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                        Today
                    </button>
                    <button wire:click="$set('dateFilter', 'this_week')" 
                            class="px-3 py-1 text-sm rounded {{ $dateFilter === 'this_week' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                        This Week
                    </button>
                    <button wire:click="$set('dateFilter', 'this_month')" 
                            class="px-3 py-1 text-sm rounded {{ $dateFilter === 'this_month' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                        This Month
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('order_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Order Number
                        </th>
                        <th wire:click="sortBy('supplier_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Supplier
                        </th>
                        <th wire:click="sortBy('order_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Order Date
                        </th>
                        <th wire:click="sortBy('expected_delivery_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Expected Delivery
                        </th>
                        <th wire:click="sortBy('total_amount')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Total Amount
                        </th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchaseOrders as $purchaseOrder)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $purchaseOrder->order_number }}</div>
                                <div class="text-sm text-gray-500">{{ $purchaseOrder->items->count() }} items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $purchaseOrder->supplier->name }}</div>
                                <div class="text-sm text-gray-500">{{ $purchaseOrder->supplier->contact_person ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchaseOrder->order_date->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M j, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($purchaseOrder->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purchaseOrder->getStatusBadgeClass() }}">
                                    {{ ucfirst($purchaseOrder->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="updateStatus({{ $purchaseOrder->id }}, 'approved')" 
                                            class="text-green-600 hover:text-green-900"
                                            @if($purchaseOrder->status !== 'pending') disabled @endif>
                                        Approve
                                    </button>
                                    <button wire:click="updateStatus({{ $purchaseOrder->id }}, 'ordered')" 
                                            class="text-blue-600 hover:text-blue-900"
                                            @if($purchaseOrder->status !== 'approved') disabled @endif>
                                        Mark Ordered
                                    </button>
                                    <button wire:click="updateStatus({{ $purchaseOrder->id }}, 'received')" 
                                            class="text-purple-600 hover:text-purple-900"
                                            @if($purchaseOrder->status !== 'ordered') disabled @endif>
                                        Mark Received
                                    </button>
                                    <button wire:click="deletePurchaseOrder({{ $purchaseOrder->id }})" 
                                            wire:confirm="Are you sure you want to delete this purchase order?"
                                            class="text-red-600 hover:text-red-900"
                                            @if($purchaseOrder->status !== 'draft') disabled @endif>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase orders found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No purchase orders match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $purchaseOrders->links() }}
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