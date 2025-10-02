<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daily Sales Reporting</h1>
                <p class="text-gray-600">Comprehensive daily sales analytics and reporting</p>
            </div>
            <div class="flex space-x-4">
                <button 
                    wire:click="exportToCSV" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
                <a href="{{ route('admin.pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    POS Terminal
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Date</label>
                <input 
                    type="date" 
                    wire:model.live="selectedDate" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select wire:model.live="reportType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="summary">Summary</option>
                    <option value="hourly">Hourly Breakdown</option>
                    <option value="staff">Staff Performance</option>
                    <option value="detailed">Detailed Transactions</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select wire:model.live="paymentMethodFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                <select wire:model.live="staffFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Staff</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model.live="showComparison" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700">Compare with Previous Day</span>
                </label>
            </div>
        </div>
        
        @if($showComparison)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Comparison Date</label>
                <input 
                    type="date" 
                    wire:model.live="comparisonDate" 
                    class="w-full md:w-64 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($summary['total_revenue'], 2) }}</p>
                    @if($showComparison && $comparisonData)
                        <p class="text-xs text-gray-500">
                            vs ${{ number_format($comparisonData['total_revenue'], 2) }}
                            @php
                                $change = $summary['total_revenue'] - $comparisonData['total_revenue'];
                                $percentChange = $comparisonData['total_revenue'] > 0 ? ($change / $comparisonData['total_revenue']) * 100 : 0;
                            @endphp
                            <span class="{{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ $change >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%)
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $summary['total_transactions'] }}</p>
                    @if($showComparison && $comparisonData)
                        <p class="text-xs text-gray-500">
                            vs {{ $comparisonData['total_transactions'] }}
                            @php
                                $change = $summary['total_transactions'] - $comparisonData['total_transactions'];
                                $percentChange = $comparisonData['total_transactions'] > 0 ? ($change / $comparisonData['total_transactions']) * 100 : 0;
                            @endphp
                            <span class="{{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ $change >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%)
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Transaction</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($summary['average_transaction_value'], 2) }}</p>
                    @if($showComparison && $comparisonData)
                        <p class="text-xs text-gray-500">
                            vs ${{ number_format($comparisonData['average_transaction_value'], 2) }}
                            @php
                                $change = $summary['average_transaction_value'] - $comparisonData['average_transaction_value'];
                                $percentChange = $comparisonData['average_transaction_value'] > 0 ? ($change / $comparisonData['average_transaction_value']) * 100 : 0;
                            @endphp
                            <span class="{{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ({{ $change >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%)
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cash Drawers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $cashDrawerData->count() }}</p>
                    <p class="text-xs text-gray-500">{{ $cashDrawerData->where('status', 'open')->count() }} open</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    @if($reportType === 'summary')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Payment Method Breakdown -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Method Breakdown</h3>
                </div>
                <div class="p-6">
                    @forelse($summary['payment_method_breakdown'] as $payment)
                        <div class="flex justify-between items-center py-3 border-b">
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->count }} transactions</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">${{ number_format($payment->total, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format(($payment->total / $summary['total_revenue']) * 100, 1) }}%</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>No payment data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Top Products</h3>
                </div>
                <div class="p-6">
                    @forelse($summary['top_products'] as $product)
                        <div class="flex justify-between items-center py-3 border-b">
                            <div>
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->invoice_items_count }} sold</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">${{ number_format($product->total_revenue ?? 0, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>No product data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Top Clients</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($summary['top_clients'] as $client)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->invoices_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($client->total_spent ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format(($client->total_spent ?? 0) / max($client->invoices_count, 1), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No client data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType === 'hourly')
        <!-- Hourly Breakdown -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Hourly Sales Breakdown</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hour</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($hourlyData as $hour)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $hour['hour_label'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($hour['revenue'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $hour['transactions'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($hour['transactions'] > 0 ? $hour['revenue'] / $hour['transactions'] : 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $summary['total_revenue'] > 0 ? number_format(($hour['revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType === 'staff')
        <!-- Staff Performance -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Staff Performance</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($staffBreakdown as $staff)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $staff['user_name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($staff['revenue'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $staff['transactions'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($staff['average_transaction'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $summary['total_revenue'] > 0 ? number_format(($staff['revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No staff data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType === 'detailed')
        <!-- Detailed Transactions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Detailed Transactions</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($detailedTransactions as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->payment_date->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $payment->invoice->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->invoice->client->name ?? 'Walk-in' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($payment->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->invoice->items->count() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $detailedTransactions->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- Cash Drawer Summary -->
    @if($cashDrawerData->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Cash Drawer Summary</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opening</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Closing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cashDrawerData as $drawer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $drawer->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($drawer->status === 'open') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($drawer->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($drawer->opening_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $drawer->closing_amount ? '$' . number_format($drawer->closing_amount, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $drawer->expected_amount ? '$' . number_format($drawer->expected_amount, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($drawer->difference !== null)
                                            <span class="
                                                @if($drawer->difference > 0) text-green-600
                                                @elseif($drawer->difference < 0) text-red-600
                                                @else text-gray-600
                                                @endif">
                                                {{ $drawer->difference > 0 ? '+' : '' }}${{ number_format($drawer->difference, 2) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
