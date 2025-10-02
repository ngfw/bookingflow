<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Inventory Reports & Analytics</h1>
                <p class="text-gray-600">Comprehensive inventory insights and analytics</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Date Range:</label>
                <select wire:model.live="dateRange" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overview['total_products'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Products</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overview['active_products'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Low Stock</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overview['low_stock_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Out of Stock</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overview['out_of_stock_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Value</h3>
            <p class="text-3xl font-bold text-blue-600">${{ number_format($overview['total_value'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-2">Total current inventory value</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Usage Cost</h3>
            <p class="text-3xl font-bold text-green-600">${{ number_format($overview['total_usage'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-2">Total product usage cost</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Cost</h3>
            <p class="text-3xl font-bold text-purple-600">${{ number_format($overview['total_purchases'], 2) }}</p>
            <p class="text-sm text-gray-500 mt-2">Total purchase orders</p>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert</h3>
                <p class="text-sm text-gray-500">Products below minimum stock level</p>
            </div>
            <div class="p-6">
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-yellow-800">{{ $product->current_stock }} / {{ $product->minimum_stock }}</p>
                                    <p class="text-xs text-gray-500">units</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No low stock products</p>
                @endif
            </div>
        </div>

        <!-- Top Used Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Used Products</h3>
                <p class="text-sm text-gray-500">Most frequently used products</p>
            </div>
            <div class="p-6">
                @if($topUsedProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($topUsedProducts as $usage)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $usage->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $usage->product->category->name ?? 'No Category' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $usage->total_quantity }} units</p>
                                    <p class="text-xs text-gray-500">${{ number_format($usage->total_cost, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No usage data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Supplier Performance -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Supplier Performance</h3>
            <p class="text-sm text-gray-500">Top performing suppliers by purchase volume</p>
        </div>
        <div class="p-6">
            @if($supplierPerformance->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($supplierPerformance as $supplier)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $supplier->supplier->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $supplier->order_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($supplier->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($supplier->avg_order_value, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No supplier data available</p>
            @endif
        </div>
    </div>

    <!-- Purchase Order Summary -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Purchase Order Summary</h3>
            <p class="text-sm text-gray-500">Purchase order status breakdown</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $purchaseOrderSummary['total_orders'] }}</p>
                    <p class="text-sm text-gray-500">Total Orders</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-600">{{ $purchaseOrderSummary['draft_orders'] }}</p>
                    <p class="text-sm text-gray-500">Draft</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $purchaseOrderSummary['pending_orders'] }}</p>
                    <p class="text-sm text-gray-500">Pending</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $purchaseOrderSummary['approved_orders'] }}</p>
                    <p class="text-sm text-gray-500">Approved</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $purchaseOrderSummary['ordered_orders'] }}</p>
                    <p class="text-sm text-gray-500">Ordered</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $purchaseOrderSummary['received_orders'] }}</p>
                    <p class="text-sm text-gray-500">Received</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $purchaseOrderSummary['cancelled_orders'] }}</p>
                    <p class="text-sm text-gray-500">Cancelled</p>
                </div>
            </div>
            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($purchaseOrderSummary['total_value'], 2) }}</p>
                    <p class="text-sm text-gray-500">Total Order Value</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($purchaseOrderSummary['avg_order_value'], 2) }}</p>
                    <p class="text-sm text-gray-500">Average Order Value</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Analysis -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Cost Analysis</h3>
            <p class="text-sm text-gray-500">Product usage and cost insights</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">${{ number_format($costAnalysis['usage_cost'], 2) }}</p>
                    <p class="text-sm text-gray-500">Total Usage Cost</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($costAnalysis['purchase_cost'], 2) }}</p>
                    <p class="text-sm text-gray-500">Total Purchase Cost</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">${{ number_format($costAnalysis['cost_per_service'], 2) }}</p>
                    <p class="text-sm text-gray-500">Cost Per Service</p>
                </div>
            </div>
            
            @if($costAnalysis['cost_trend'] != 0)
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">Cost Trend vs Previous Period</p>
                    <p class="text-lg font-semibold {{ $costAnalysis['cost_trend'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $costAnalysis['cost_trend'] > 0 ? '+' : '' }}{{ number_format($costAnalysis['cost_trend'], 1) }}%
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>