<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Manage Your Appointment</h1>
            <p class="text-xl text-gray-600">View, modify, or cancel your booking</p>
        </div>

        <!-- Search Form -->
        @if(!$appointment)
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Find Your Appointment</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Appointment ID</label>
                        <input wire:model="appointmentId" type="text" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" 
                               placeholder="Enter your appointment ID">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input wire:model="clientEmail" type="email" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" 
                               placeholder="Enter your email address">
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        You can find your appointment ID in the confirmation email we sent you.
                    </p>
                </div>
            </div>
        @endif

        <!-- Appointment Details -->
        @if($appointment)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Appointment Header -->
                <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Appointment #{{ $appointment->id }}</h2>
                            <p class="text-pink-100">{{ $appointment->service->name }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-pink-100">Status</div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($appointment->status === 'no_show') bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Appointment Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service:</span>
                                    <span class="font-medium">{{ $appointment->service->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-medium">{{ $appointment->service->duration_minutes }} minutes</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Staff:</span>
                                    <span class="font-medium">{{ $appointment->staff->user->name ?? 'Unassigned' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Price:</span>
                                    <span class="font-medium text-pink-600">${{ number_format($appointment->total_price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Client Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-medium">{{ $appointment->client->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $appointment->client->user->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium">{{ $appointment->client->user->phone }}</span>
                                </div>
                            </div>
                            
                            @if($appointment->notes)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notes:</h4>
                                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $appointment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                @if(!$showModifyForm)
                                    <button wire:click="startModification" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Reschedule Appointment
                                    </button>
                                    <button wire:click="cancelAppointment" 
                                            wire:confirm="Are you sure you want to cancel this appointment?"
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Cancel Appointment
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modification Form -->
            @if($showModifyForm)
                <div class="bg-white rounded-2xl shadow-xl p-8 mt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Reschedule Appointment</h2>
                        <button wire:click="cancelModification" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @if($modificationStep === 1)
                        <!-- Step 1: Select New Date -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Select New Date</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach($availableDates as $date)
                                    <button wire:click="selectNewDate('{{ $date['date'] }}')" 
                                            class="p-3 rounded-lg border-2 text-center transition-colors {{ $newDate == $date['date'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }}">
                                        <div class="text-sm font-medium">{{ $date['day'] }}</div>
                                        <div class="text-xs text-gray-600">{{ $date['display'] }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 2: Select New Time -->
                        @if($newDate)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Select New Time</h3>
                                @if(count($availableTimeSlots) > 0)
                                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                        @foreach($availableTimeSlots as $slot)
                                            <button wire:click="selectNewTimeSlot({{ json_encode($slot) }})" 
                                                    class="p-3 rounded-lg border-2 text-center transition-colors {{ $newTime == $slot['time'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }}">
                                                <div class="text-sm font-medium">{{ $slot['display'] }}</div>
                                                <div class="text-xs text-gray-600">{{ $slot['staff_name'] }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <p>No available time slots for this date.</p>
                                        <p class="text-sm">Please select a different date.</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Confirmation -->
                        @if($newTime)
                            <div class="bg-pink-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Changes</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">New Date:</span>
                                        <span class="font-medium">{{ Carbon\Carbon::parse($newDate)->format('M j, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">New Time:</span>
                                        <span class="font-medium">{{ Carbon\Carbon::parse($newTime)->format('g:i A') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center space-x-4">
                                <button wire:click="confirmModification" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    Confirm Reschedule
                                </button>
                                <button wire:click="cancelModification" 
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    Cancel
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        @endif

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

        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="/" class="text-pink-600 hover:text-pink-700 font-medium">
                ← Back to Home
            </a>
        </div>
    </div>
</div>