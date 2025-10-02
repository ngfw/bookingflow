<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Receipt Printing</h1>
                <p class="text-gray-600">Print and manage customer receipts</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Invoices -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Sales</h3>
                    <p class="text-sm text-gray-600">Select an invoice to print receipt</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentInvoices as $invoice)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" 
                                 wire:click="setInvoice({{ $invoice->id }})"
                                 @if($invoice->id == $invoiceId) class="border-blue-500 bg-blue-50" @endif>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $invoice->invoice_number }}</h4>
                                        <p class="text-sm text-gray-600">
                                            @if($invoice->client)
                                                {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                            @else
                                                Walk-in Customer
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $invoice->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-green-600">${{ number_format($invoice->total_amount, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($invoice->status) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent sales</h3>
                                <p class="mt-1 text-sm text-gray-500">Complete a sale to see receipts here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Preview & Actions -->
        <div class="lg:col-span-1">
            @if($invoice)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Receipt Preview</h3>
                    
                    <!-- Invoice Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Invoice #:</span>
                            <span class="text-sm font-medium">{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Date:</span>
                            <span class="text-sm font-medium">{{ $invoice->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($invoice->client)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Client:</span>
                                <span class="text-sm font-medium">{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total:</span>
                            <span class="text-sm font-semibold text-green-600">${{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Print Options -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Print Options</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_logo" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Logo</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_address" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Address</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_phone" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Phone</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_email" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_website" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Website</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_tax_id" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Tax ID</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="printOptions.include_footer" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Include Footer</span>
                            </label>
                        </div>
                    </div>

                    <!-- Paper Size -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Paper Size</label>
                        <select wire:model="printOptions.paper_size" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="thermal">Thermal (58mm)</option>
                            <option value="a4">A4</option>
                            <option value="letter">Letter</option>
                        </select>
                    </div>

                    <!-- Font Size -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
                        <select wire:model="printOptions.font_size" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button 
                            wire:click="printReceipt" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Receipt
                        </button>

                        <button 
                            wire:click="printThermalReceipt" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-medium flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Thermal Receipt
                        </button>

                        @if($invoice->client && $invoice->client->email)
                            <button 
                                wire:click="emailReceipt" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md font-medium flex items-center justify-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email Receipt
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Select an Invoice</h3>
                        <p class="mt-1 text-sm text-gray-500">Choose an invoice from the list to preview and print receipt.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Print Dialog Modal -->
    @if($showPrintDialog)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Print Receipt</h3>
                        <p class="text-sm text-gray-600 mb-6">Receipt will be sent to the default printer.</p>
                        
                        <div class="flex space-x-2">
                            <button 
                                wire:click="printReceipt" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium"
                            >
                                Print
                            </button>
                            <button 
                                wire:click="hidePrintDialog" 
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md font-medium"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
