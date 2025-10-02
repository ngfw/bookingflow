<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
                <p class="text-gray-600">Process and manage payments</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.payments.refunds') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Refunds
                </a>
                <a href="{{ route('admin.payments.receipt') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Receipts
                </a>
                <a href="{{ route('admin.payments.process') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Process Payment
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Invoices
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
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by payment number, reference, transaction ID, or client..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Method Filter -->
<div>
                    <select wire:model.live="methodFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="digital_wallet">Digital Wallet</option>
                        <option value="check">Check</option>
                        <option value="other">Other</option>
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

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('payment_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Payment Number
                        </th>
                        <th wire:click="sortBy('client_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Client
                        </th>
                        <th wire:click="sortBy('payment_method')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Method
                        </th>
                        <th wire:click="sortBy('amount')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Amount
                        </th>
                        <th wire:click="sortBy('payment_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Payment Date
                        </th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->payment_number }}</div>
                                @if($payment->reference_number)
                                    <div class="text-sm text-gray-500">Ref: {{ $payment->reference_number }}</div>
                                @endif
                                @if($payment->transaction_id)
                                    <div class="text-sm text-gray-500">Txn: {{ $payment->transaction_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->client->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $payment->client->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg mr-2">{{ $payment->getPaymentMethodIcon() }}</span>
                                    <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-semibold">${{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->getStatusBadgeClass() }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="updateStatus({{ $payment->id }}, 'completed')" 
                                            class="text-green-600 hover:text-green-900"
                                            @if($payment->status === 'completed' || $payment->status === 'cancelled') disabled @endif>
                                        Complete
                                    </button>
                                    <button wire:click="updateStatus({{ $payment->id }}, 'failed')" 
                                            class="text-red-600 hover:text-red-900"
                                            @if($payment->status === 'completed' || $payment->status === 'cancelled') disabled @endif>
                                        Fail
                                    </button>
                                    <button wire:click="processRefund({{ $payment->id }})" 
                                            class="text-blue-600 hover:text-blue-900"
                                            @if($payment->status !== 'completed') disabled @endif>
                                        Refund
                                    </button>
                                    <button wire:click="updateStatus({{ $payment->id }}, 'cancelled')" 
                                            class="text-gray-600 hover:text-gray-900"
                                            @if($payment->status === 'completed' || $payment->status === 'cancelled') disabled @endif>
                                        Cancel
                                    </button>
                                    <button wire:click="deletePayment({{ $payment->id }})" 
                                            wire:confirm="Are you sure you want to delete this payment?"
                                            class="text-red-600 hover:text-red-900"
                                            @if($payment->status !== 'pending') disabled @endif>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No payments match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $payments->links() }}
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