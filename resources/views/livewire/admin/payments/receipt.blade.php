<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Receipt</h1>
                <p class="text-gray-600">Generate and print payment receipts</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Payments
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Payment Selection -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Payment</h3>
                    
                    <div class="mb-4">
                        <label for="paymentId" class="block text-sm font-medium text-gray-700">Payment</label>
                        <select wire:model.live="paymentId" id="paymentId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a payment</option>
                            @foreach($payments as $payment)
                                <option value="{{ $payment->id }}">
                                    {{ $payment->payment_number }} - {{ $payment->client->user->name }} 
                                    (${{ number_format($payment->amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($payment)
                        <div class="space-y-4">
                            <div class="flex space-x-2">
                                <button wire:click="generatePDF" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download PDF
                                </button>
                                <button wire:click="printReceipt" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Print
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Receipt Preview -->
            <div class="lg:col-span-2">
                @if($payment && $invoice && $client)
                    <div class="bg-white rounded-lg shadow p-8 print-receipt" id="receipt-content">
                        <!-- Receipt Header -->
                        <div class="text-center mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">BookingFlow Management</h1>
                            <p class="text-gray-600">123 Beauty Street, City, State 12345</p>
                            <p class="text-gray-600">Phone: (555) 123-4567 | Email: info@bookingflow.com</p>
                            <p class="text-gray-600">Website: www.bookingflow.com</p>
                        </div>

                        <!-- Receipt Title -->
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">PAYMENT RECEIPT</h2>
                            <p class="text-gray-600">Receipt #{{ $payment->payment_number }}</p>
                        </div>

                        <!-- Receipt Details -->
                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Client Information</h3>
                                <div class="text-gray-700">
                                    <p><strong>Name:</strong> {{ $client->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $client->user->email }}</p>
                                    @if($client->phone)
                                        <p><strong>Phone:</strong> {{ $client->phone }}</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Details</h3>
                                <div class="text-gray-700">
                                    <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('M j, Y') }}</p>
                                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                    <p><strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}</p>
                                    @if($payment->reference_number)
                                        <p><strong>Reference:</strong> {{ $payment->reference_number }}</p>
                                    @endif
                                    @if($payment->transaction_id)
                                        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Details</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $item->item_name }}
                                                    @if($item->description)
                                                        <br><span class="text-gray-500">{{ $item->description }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="border-t pt-6">
                            <div class="max-w-md ml-auto">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
                                    </div>
                                    @if($invoice->discount_amount > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Discount:</span>
                                            <span class="font-semibold text-green-600">-${{ number_format($invoice->discount_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    @if($invoice->tax_amount > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tax ({{ $invoice->tax_rate }}%):</span>
                                            <span class="font-semibold">${{ number_format($invoice->tax_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="border-t pt-2">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span>Total Paid:</span>
                                            <span class="text-green-600">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-12 text-center text-gray-500">
                            <p>Thank you for your business!</p>
                            <p class="text-sm mt-2">Receipt generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
                            @if($payment->processedBy)
                                <p class="text-sm">Processed by: {{ $payment->processedBy->name }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No payment selected</h3>
                            <p class="mt-1 text-sm text-gray-500">Please select a payment to view the receipt.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .print-receipt {
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Print Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-receipt', () => {
                window.print();
            });
        });
    </script>

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