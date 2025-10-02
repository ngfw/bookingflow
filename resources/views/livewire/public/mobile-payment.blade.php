<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-md mx-auto px-4 py-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Mobile Payment</h1>
            <p class="text-md text-gray-600">Secure payment processing</p>
        </div>

        @if($currentStep === 'search')
            <!-- Invoice Search -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Find Your Invoice</h2>
                
                <form wire:submit.prevent="searchInvoice" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input wire:model="clientEmail" type="email" 
                               placeholder="Enter your email address" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                        @error('clientEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input wire:model="clientPhone" type="tel" 
                               placeholder="Enter your phone number" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                        @error('clientPhone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold text-lg">
                        Find Invoice
                    </button>
                </form>
            </div>

        @elseif($currentStep === 'payment' && $invoice)
            <!-- Payment Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Details</h2>
                
                <!-- Invoice Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Invoice Summary</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Invoice #:</span>
                            <span class="font-medium">{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Client:</span>
                            <span class="font-medium">{{ $client->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $invoice->invoice_date->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                            <span>Total Amount:</span>
                            <span class="text-green-600">${{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $paymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model="paymentMethod" value="card" class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Credit/Debit Card</h3>
                                    <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $paymentMethod === 'digital_wallet' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model="paymentMethod" value="digital_wallet" class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Digital Wallet</h3>
                                    <p class="text-sm text-gray-600">Apple Pay, Google Pay, PayPal</p>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $paymentMethod === 'bank_transfer' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" wire:model="paymentMethod" value="bank_transfer" class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Bank Transfer</h3>
                                    <p class="text-sm text-gray-600">Direct bank transfer</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Payment Amount</label>
                    <div class="space-y-3">
                        <input wire:model.live="amount" type="number" step="0.01" min="0.01" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                        
                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($quickAmounts as $quickAmount)
                                <button wire:click="setQuickAmount({{ $quickAmount }})" 
                                        class="py-2 px-3 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm {{ $amount == $quickAmount ? 'bg-blue-50 border-blue-300' : '' }}">
                                    ${{ $quickAmount }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Payment Details Based on Method -->
                @if($paymentMethod === 'card')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <input wire:model="cardNumber" type="text" 
                                   placeholder="1234 5678 9012 3456" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('cardNumber') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                <input wire:model="cardExpiry" type="text" 
                                       placeholder="MM/YY" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('cardExpiry') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                <input wire:model="cardCvv" type="text" 
                                       placeholder="123" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('cardCvv') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <input wire:model="cardHolderName" type="text" 
                                   placeholder="John Doe" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('cardHolderName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                @elseif($paymentMethod === 'digital_wallet')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Digital Wallet</label>
                            <select wire:model="walletType" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                <option value="apple_pay">Apple Pay</option>
                                <option value="google_pay">Google Pay</option>
                                <option value="paypal">PayPal</option>
                            </select>
                            @error('walletType') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wallet Token</label>
                            <input wire:model="walletToken" type="text" 
                                   placeholder="Enter wallet token" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('walletToken') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                @elseif($paymentMethod === 'bank_transfer')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Account Number</label>
                            <input wire:model="bankAccount" type="text" 
                                   placeholder="Enter account number" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('bankAccount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Routing Number</label>
                            <input wire:model="bankRouting" type="text" 
                                   placeholder="Enter routing number" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('bankRouting') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                <!-- Security Notice -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h3 class="font-semibold text-blue-900">Secure Payment</h3>
                    </div>
                    <p class="text-sm text-blue-800">Your payment information is encrypted and secure. We use industry-standard SSL encryption to protect your data.</p>
                </div>

                <!-- Process Payment Button -->
                <button wire:click="processPayment" 
                        wire:loading.attr="disabled"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white py-4 rounded-lg font-semibold text-lg">
                    <span wire:loading.remove>Process Payment - ${{ number_format($amount, 2) }}</span>
                    <span wire:loading>
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>

        @elseif($currentStep === 'confirmation' && $paymentSuccessful)
            <!-- Payment Success -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                    <p class="text-gray-600">Your payment has been processed successfully</p>
                </div>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Receipt</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Reference:</span>
                            <span class="font-medium">{{ $paymentReference }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transaction ID:</span>
                            <span class="font-medium">{{ $transactionId }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $paymentMethod)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount Paid:</span>
                            <span class="font-medium">${{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Date:</span>
                            <span class="font-medium">{{ $paymentDate->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">What's Next?</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• You will receive a payment confirmation email</li>
                        <li>• Your invoice has been marked as paid</li>
                        <li>• Any associated appointments are now confirmed</li>
                        <li>• Keep this receipt for your records</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <button wire:click="newPayment" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium">
                        Make Another Payment
                    </button>
                    <a href="/" 
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium text-center block">
                        Back to Home
                    </a>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="fixed bottom-4 left-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
                <div class="flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="fixed bottom-4 left-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
                <div class="flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

