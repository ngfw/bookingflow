<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <h1 class="text-lg font-bold text-gray-900">Manage Booking</h1>
            <p class="text-sm text-gray-600">Find and manage your appointments</p>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="px-4 py-6">
        @if(empty($appointments))
            <!-- Search Form -->
            <div class="bg-white rounded-xl p-4 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Find Your Appointments</h2>
                
                <form wire:submit.prevent="searchAppointments" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input wire:model="email" type="email" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4"
                               placeholder="Enter your email address"
                               autofocus>
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-medium {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $loading ? 'disabled' : '' }}>
                        @if($loading)
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                                Searching...
                            </div>
                        @else
                            Find Appointments
                        @endif
                    </button>
                </form>
            </div>

            @if(session()->has('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

        @else
            <!-- Appointments List -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Your Appointments</h2>
                    <button wire:click="$set('appointments', [])" class="text-pink-600 text-sm font-medium">
                        Search Again
                    </button>
                </div>

                @foreach($appointments as $appointment)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $appointment->service->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $appointment->staff->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-pink-600">${{ number_format($appointment->total_price, 2) }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->service->duration_minutes }}min</div>
                                </div>
                            </div>

                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $appointment->appointment_date->format('M j, Y') }} at {{ $appointment->appointment_date->format('g:i A') }}
                            </div>

                            <div class="flex items-center mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>

                            @if($appointment->notes)
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-sm text-gray-700">{{ $appointment->notes }}</p>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                    <button wire:click="showRescheduleConfirmation({{ $appointment->id }})" 
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                                        Reschedule
                                    </button>
                                    <button wire:click="showCancelConfirmation({{ $appointment->id }})" 
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Cancel Confirmation Modal -->
    @if($showCancelModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
            <div class="bg-white rounded-t-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Appointment</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to cancel this appointment?</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="text-sm">
                            <div class="font-medium">{{ $selectedAppointment->service->name }}</div>
                            <div class="text-gray-600">{{ $selectedAppointment->appointment_date->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button wire:click="$set('showCancelModal', false)" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-medium">
                            Keep Appointment
                        </button>
                        <button wire:click="cancelAppointment" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium">
                            Cancel Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reschedule Modal -->
    @if($showRescheduleModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
            <div class="bg-white rounded-t-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reschedule Appointment</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="text-sm">
                            <div class="font-medium">{{ $selectedAppointment->service->name }}</div>
                            <div class="text-gray-600">Current: {{ $selectedAppointment->appointment_date->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>

                    <form wire:submit.prevent="rescheduleAppointment" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                            <input wire:model="newDate" type="date" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4"
                                   min="{{ now()->format('Y-m-d') }}">
                        </div>

                        @if($newDate)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                                @if(count($availableTimeSlots) > 0)
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($availableTimeSlots as $slot)
                                            <button type="button" wire:click="$set('newTime', '{{ $slot['time'] }}')" 
                                                    class="py-2 px-3 rounded-lg border-2 text-center text-sm {{ $newTime == $slot['time'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                                                {{ $slot['display'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No available times for this date.</p>
                                @endif
                            </div>
                        @endif

                        <div class="flex space-x-3 pt-4">
                            <button type="button" wire:click="$set('showRescheduleModal', false)" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-medium {{ !$newTime ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !$newTime ? 'disabled' : '' }}>
                                Reschedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Success/Error Messages -->
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

