<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50">
    <div class="max-w-md mx-auto px-4 py-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Check In</h1>
            <p class="text-md text-gray-600">Welcome to our salon</p>
        </div>

        @if(!$checkInSuccessful)
            <!-- Check-in Method Selection -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">How would you like to check in?</h2>
                
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $checkInMethod === 'phone' ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model="checkInMethod" value="phone" class="sr-only">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Phone Number</h3>
                                <p class="text-sm text-gray-600">Enter your phone number</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $checkInMethod === 'email' ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model="checkInMethod" value="email" class="sr-only">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email Address</h3>
                                <p class="text-sm text-gray-600">Enter your email address</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer {{ $checkInMethod === 'qr' ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model="checkInMethod" value="qr" class="sr-only">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">QR Code</h3>
                                <p class="text-sm text-gray-600">Scan your appointment QR code</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Input Fields -->
                <div class="space-y-4">
                    @if($checkInMethod === 'phone')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input wire:model="phoneNumber" type="tel" 
                                   placeholder="Enter your phone number" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4">
                            @error('phoneNumber') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @elseif($checkInMethod === 'email')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input wire:model="email" type="email" 
                                   placeholder="Enter your email address" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @elseif($checkInMethod === 'qr')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">QR Code / Appointment Number</label>
                            <input wire:model="qrCode" type="text" 
                                   placeholder="Enter QR code or appointment number" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4">
                            @error('qrCode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <button wire:click="searchAppointment" 
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-semibold text-lg">
                        Find My Appointment
                    </button>
                </div>
            </div>

            <!-- Appointment Details -->
            @if($showAppointmentDetails && $appointment)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment Details</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $client->user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $client->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $appointment->service->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $appointment->service->duration_minutes }} minutes</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}</h3>
                                <p class="text-sm text-gray-600">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $appointment->staff->user->name }}</h3>
                                <p class="text-sm text-gray-600">Your stylist</p>
                            </div>
                        </div>
                    </div>

                    <!-- Check-in Button -->
                    <div class="mt-6">
                        <button wire:click="checkIn" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold text-lg">
                            Check In Now
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <button wire:click="rescheduleAppointment" 
                                class="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
                            Reschedule
                        </button>
                        <button wire:click="cancelAppointment" 
                                wire:confirm="Are you sure you want to cancel this appointment?"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            @endif

        @else
            <!-- Check-in Success -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Check-in Successful!</h2>
                    <p class="text-gray-600">You're all set for your appointment</p>
                </div>

                @if($appointment)
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Appointment Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span class="font-medium">{{ $appointment->service->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Staff:</span>
                                <span class="font-medium">{{ $appointment->staff->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Check-in:</span>
                                <span class="font-medium">{{ $checkInTime->format('g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Wait Time Information -->
                    @if($waitTime > 0)
                        <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="font-semibold text-yellow-900">Wait Time</h3>
                            </div>
                            <p class="text-sm text-yellow-800">
                                Your appointment is in {{ $waitTime }} minutes. 
                                @if($estimatedWaitTime > 0)
                                    Estimated wait time: {{ $estimatedWaitTime }} minutes.
                                @endif
                            </p>
                        </div>
                    @elseif($staffReady)
                        <div class="bg-green-50 rounded-lg p-4 mb-6">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <h3 class="font-semibold text-green-900">Ready!</h3>
                            </div>
                            <p class="text-sm text-green-800">Your stylist is ready for you. Please proceed to the service area.</p>
                        </div>
                    @endif

                    <!-- Instructions -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">What's Next?</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Please wait in the reception area</li>
                            <li>• Your stylist will call you when ready</li>
                            <li>• You can browse our retail products while waiting</li>
                            <li>• Enjoy complimentary refreshments</li>
                        </ul>
                    </div>
                @endif

                <div class="space-y-3">
                    <button wire:click="newCheckIn" 
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-medium">
                        Check In Another Appointment
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

