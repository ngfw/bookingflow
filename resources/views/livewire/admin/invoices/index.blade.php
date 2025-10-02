<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoice Management</h1>
                <p class="text-gray-600">Manage invoices and billing</p>
            </div>
            <div class="flex space-x-4">
                <button wire:click="markOverdue" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark Overdue
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Payments
                </a>
                <a href="{{ route('admin.invoices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by invoice number or client..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
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
                    <button wire:click="$set('dateFilter', 'overdue')" 
                            class="px-3 py-1 text-sm rounded {{ $dateFilter === 'overdue' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                        Overdue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('invoice_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Invoice Number
                        </th>
                        <th wire:click="sortBy('client_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Client
                        </th>
                        <th wire:click="sortBy('invoice_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Invoice Date
                        </th>
                        <th wire:click="sortBy('due_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Due Date
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
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                                @if($invoice->appointment)
                                    <div class="text-sm text-gray-500">{{ $invoice->appointment->service->name ?? 'Service' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->client->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->client->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->invoice_date->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="{{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $invoice->due_date->format('M j, Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-semibold">${{ number_format($invoice->total_amount, 2) }}</div>
                                @if($invoice->balance_due > 0)
                                    <div class="text-sm text-red-600">Balance: ${{ number_format($invoice->balance_due, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->getStatusBadgeClass() }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="updateStatus({{ $invoice->id }}, 'sent')" 
                                            class="text-blue-600 hover:text-blue-900"
                                            @if($invoice->status !== 'draft') disabled @endif>
                                        Send
                                    </button>
                                    <button wire:click="updateStatus({{ $invoice->id }}, 'paid')" 
                                            class="text-green-600 hover:text-green-900"
                                            @if($invoice->status === 'paid' || $invoice->status === 'cancelled') disabled @endif>
                                        Mark Paid
                                    </button>
                                    <button wire:click="updateStatus({{ $invoice->id }}, 'cancelled')" 
                                            class="text-red-600 hover:text-red-900"
                                            @if($invoice->status === 'paid' || $invoice->status === 'cancelled') disabled @endif>
                                        Cancel
                                    </button>
                                    <button wire:click="deleteInvoice({{ $invoice->id }})" 
                                            wire:confirm="Are you sure you want to delete this invoice?"
                                            class="text-red-600 hover:text-red-900"
                                            @if($invoice->status !== 'draft') disabled @endif>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No invoices match your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $invoices->links() }}
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