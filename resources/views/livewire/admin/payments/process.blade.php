<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Process Payment</h1>
                <p class="text-gray-600">Record a new payment for an invoice</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Payments
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <form wire:submit.prevent="processPayment">
                <div class="p-6 space-y-6">
                    <!-- Invoice Selection -->
                    <div>
                        <label for="invoiceId" class="block text-sm font-medium text-gray-700">Invoice</label>
                        <select wire:model.live="invoiceId" id="invoiceId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select an invoice</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->invoice_number }} - {{ $invoice->client->user->name }} 
                                    (Balance: ${{ number_format($invoice->balance_due, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('invoiceId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Client Information (Auto-filled) -->
                    <div>
                        <label for="clientId" class="block text-sm font-medium text-gray-700">Client</label>
                        <select wire:model="clientId" id="clientId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" disabled>
                            <option value="">Client will be auto-selected</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->user->name }} ({{ $client->user->email }})</option>
                            @endforeach
                        </select>
                        @error('clientId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Method -->
                        <div>
                            <label for="paymentMethod" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select wire:model="paymentMethod" id="paymentMethod" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="cash">💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                <option value="digital_wallet">📱 Digital Wallet</option>
                                <option value="check">📄 Check</option>
                                <option value="other">💰 Other</option>
                            </select>
                            @error('paymentMethod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label for="paymentDate" class="block text-sm font-medium text-gray-700">Payment Date</label>
                            <input wire:model="paymentDate" type="date" id="paymentDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('paymentDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input wire:model="amount" type="number" step="0.01" min="0.01" id="amount" class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                            </div>
                            @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label for="referenceNumber" class="block text-sm font-medium text-gray-700">Reference Number</label>
                            <input wire:model="referenceNumber" type="text" id="referenceNumber" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional reference number">
                            @error('referenceNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label for="transactionId" class="block text-sm font-medium text-gray-700">Transaction ID</label>
                        <input wire:model="transactionId" type="text" id="transactionId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional transaction ID">
                        @error('transactionId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional payment notes"></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Payment Details (for card/digital payments) -->
                    @if(in_array($paymentMethod, ['card', 'digital_wallet', 'bank_transfer']))
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($paymentMethod === 'card')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Card Last 4 Digits</label>
                                        <input wire:model="paymentDetails.card_last_four" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="1234">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Card Type</label>
                                        <select wire:model="paymentDetails.card_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select card type</option>
                                            <option value="visa">Visa</option>
                                            <option value="mastercard">Mastercard</option>
                                            <option value="amex">American Express</option>
                                            <option value="discover">Discover</option>
                                        </select>
                                    </div>
                                @elseif($paymentMethod === 'digital_wallet')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Wallet Type</label>
                                        <select wire:model="paymentDetails.wallet_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select wallet type</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="apple_pay">Apple Pay</option>
                                            <option value="google_pay">Google Pay</option>
                                            <option value="venmo">Venmo</option>
                                            <option value="zelle">Zelle</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Wallet Account</label>
                                        <input wire:model="paymentDetails.wallet_account" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="account@example.com">
                                    </div>
                                @elseif($paymentMethod === 'bank_transfer')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                                        <input wire:model="paymentDetails.bank_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Bank name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Account Last 4 Digits</label>
                                        <input wire:model="paymentDetails.account_last_four" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="1234">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Process Payment
                    </button>
                </div>
            </form>
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