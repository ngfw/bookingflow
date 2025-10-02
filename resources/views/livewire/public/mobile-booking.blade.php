<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50">
    <!-- Enhanced Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button onclick="history.back()" class="p-2 text-gray-500 hover:text-gray-700 touch-target">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg font-bold text-gray-900 ml-2">Book Appointment</h1>
                </div>
                @if($currentStep > 1)
                    <button wire:click="previousStep" class="p-2 text-gray-500 hover:text-gray-700 touch-target">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                @endif
            </div>
            
            <!-- Enhanced Mobile Progress Bar -->
            <div class="mt-3">
                <div class="mobile-progress">
                    <div class="mobile-progress-bar" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
                </div>
                <div class="mt-2 text-center">
                    <span class="text-sm font-medium text-gray-600">
                        @if($currentStep === 1) Choose Service
                        @elseif($currentStep === 2) Select Time
                        @elseif($currentStep === 3) Your Info
                        @elseif($currentStep === 4) Confirm
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="px-4 py-6">
        @if($currentStep === 1)
            <!-- Step 1: Enhanced Service Selection -->
            <div class="space-y-6 mobile-booking-step">
                <!-- Enhanced Category Filter -->
                <div class="mobile-card bg-white rounded-xl p-4 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Choose Service</h2>
                    
                    <!-- Category Swipe with Enhanced UX -->
                    <div class="swipe-container flex space-x-3 overflow-x-auto pb-2">
                        <button wire:click="$set('selectedCategory', '')" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                wire:key="category-all"
                                class="swipe-item flex-shrink-0 px-4 py-2 rounded-full touch-target {{ !$selectedCategory ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                            All
                        </button>
                        @foreach($categories as $category)
                            <button wire:click="$set('selectedCategory', {{ $category->id }})" 
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    wire:key="category-{{ $category->id }}"
                                    class="swipe-item flex-shrink-0 px-4 py-2 rounded-full touch-target {{ $selectedCategory == $category->id ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Enhanced Services List -->
                <div class="space-y-3" wire:key="services-list-{{ $selectedCategory }}">
                    @foreach($services as $service)
                        <div class="mobile-service-card mobile-card bg-white rounded-xl shadow-sm overflow-hidden {{ $selectedService == $service->id ? 'selected' : '' }}" wire:key="service-{{ $service->id }}">
                            <div wire:click="selectService({{ $service->id }})" 
                                 class="p-4 cursor-pointer touch-target {{ $selectedService == $service->id ? 'bg-pink-50' : '' }}"
                                 wire:loading.attr="disabled"
                                 wire:loading.class="opacity-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $service->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($service->description, 80) }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $service->duration_minutes }}min
                                            </div>
                                            <div class="text-lg font-bold text-pink-600">${{ number_format($service->price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        @if($selectedService == $service->id)
                                            <div class="w-6 h-6 bg-pink-600 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 border-2 border-gray-300 rounded-full"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($showServiceDetails && $selectedService == $service->id)
                                <div class="px-4 pb-4 border-t border-gray-100">
                                    <div class="pt-4">
                                        <p class="text-sm text-gray-600 mb-4">{{ $service->description }}</p>
                                        <button wire:click="nextStep" 
                                                class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-medium">
                                            Select This Service
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($selectedService)
                    <div class="sticky bottom-4">
                        <button wire:click="nextStep" 
                                class="mobile-btn mobile-btn-primary w-full text-white py-4 rounded-xl font-semibold text-lg shadow-lg">
                            Continue to Time Selection
                        </button>
                    </div>
                @endif
            </div>

        @elseif($currentStep === 2)
            <!-- Step 2: Date & Time Selection (Mobile Optimized) -->
            <div class="space-y-6">
                <!-- Service Summary -->
                @if($selectedService)
                    @php $service = \App\Models\Service::find($selectedService) @endphp
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600">${{ number_format($service->price, 2) }} • {{ $service->duration_minutes }} minutes</p>
                            </div>
                            <button wire:click="$set('currentStep', 1)" class="text-pink-600 text-sm font-medium">
                                Change
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Quick Date Selection -->
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Date</h2>
                    
                    <!-- Quick Options -->
                    <div class="flex space-x-3 mb-4">
                        <button wire:click="quickSelectToday" 
                                class="flex-1 py-3 px-4 rounded-lg border-2 {{ $selectedDate == now()->format('Y-m-d') ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                            <div class="text-sm font-medium">Today</div>
                            <div class="text-xs text-gray-600">{{ now()->format('M j') }}</div>
                        </button>
                        <button wire:click="quickSelectTomorrow" 
                                class="flex-1 py-3 px-4 rounded-lg border-2 {{ $selectedDate == now()->addDay()->format('Y-m-d') ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                            <div class="text-sm font-medium">Tomorrow</div>
                            <div class="text-xs text-gray-600">{{ now()->addDay()->format('M j') }}</div>
                        </button>
                    </div>

                    <!-- Date Grid -->
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($availableDates as $date)
                            <button wire:click="selectDate('{{ $date['date'] }}')" 
                                    class="py-3 px-2 rounded-lg border-2 text-center {{ $selectedDate == $date['date'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                                <div class="text-sm font-medium">{{ $date['day'] }}</div>
                                <div class="text-xs text-gray-600">{{ $date['display'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Time Selection -->
                @if($selectedDate && $showTimePicker)
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Time</h2>
                        
                        @if(count($availableTimeSlots) > 0)
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($availableTimeSlots as $slot)
                                    <button wire:click="selectTimeSlot({{ json_encode($slot) }})" 
                                            class="py-3 px-4 rounded-lg border-2 text-center {{ $selectedTime == $slot['time'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200' }}">
                                        <div class="text-sm font-medium">{{ $slot['display'] }}</div>
                                        <div class="text-xs text-gray-600">{{ $slot['staff_name'] }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No available times for this date.</p>
                                <p class="text-sm">Please select a different date.</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if($selectedTime)
                    <div class="sticky bottom-4">
                        <button wire:click="nextStep" 
                                class="w-full bg-pink-600 hover:bg-pink-700 text-white py-4 rounded-xl font-semibold text-lg shadow-lg">
                            Continue to Information
                        </button>
                    </div>
                @endif
            </div>

        @elseif($currentStep === 3)
            <!-- Step 3: Client Information (Mobile Optimized) -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl p-4 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Information</h2>
                    
                    <form wire:submit.prevent="confirmBooking" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input wire:model="clientName" type="text" 
                                   class="mobile-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4 @error('clientName') border-red-300 @enderror"
                                   placeholder="Enter your full name">
                            @error('clientName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input wire:model="clientEmail" type="email" 
                                   class="mobile-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4 @error('clientEmail') border-red-300 @enderror"
                                   placeholder="Enter your email">
                            @error('clientEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input wire:model="clientPhone" type="tel" 
                                   class="mobile-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4 @error('clientPhone') border-red-300 @enderror"
                                   placeholder="Enter your phone number">
                            @error('clientPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                            <textarea wire:model="clientNotes" rows="3" 
                                      class="mobile-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-base py-3 px-4 @error('clientNotes') border-red-300 @enderror" 
                                      placeholder="Any special requests or notes..."></textarea>
                            @error('clientNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Appointment Summary -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Appointment Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service:</span>
                                    <span class="font-medium">{{ \App\Models\Service::find($selectedService)->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($selectedTime)->format('g:i A') }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-pink-600">${{ number_format(\App\Models\Service::find($selectedService)->price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sticky bottom-4">
                            <button type="submit" 
                                    class="mobile-btn mobile-btn-primary w-full text-white py-4 rounded-xl font-semibold text-lg shadow-lg">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($currentStep === 4)
            <!-- Step 4: Confirmation (Mobile Optimized) -->
            <div class="text-center">
                @if($bookingConfirmed)
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Booking Confirmed!</h2>
                        <p class="text-gray-600 mb-6">Your appointment has been successfully booked.</p>
                        
                        <div class="bg-pink-50 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Appointment Details</h3>
                            <div class="space-y-2 text-left">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Appointment ID:</span>
                                    <span class="font-medium">#{{ $appointmentId }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service:</span>
                                    <span class="font-medium">{{ \App\Models\Service::find($selectedService)->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ Carbon\Carbon::parse($selectedTime)->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <p class="text-gray-600">You will receive a confirmation email shortly.</p>
                            <div class="space-y-3">
                                <a href="/" class="block w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-medium">
                                    Back to Home
                                </a>
                                <button wire:click="$set('currentStep', 1)" class="block w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-medium">
                                    Book Another
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Processing Your Booking...</h2>
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-600 mx-auto"></div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Enhanced Mobile Flash Messages -->
    @if (session()->has('error'))
        <div class="mobile-toast bg-red-50 border border-red-200 text-red-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 touch-target">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Include Mobile UX Enhancements -->
@push('styles')
    {{-- Commented out potential conflicting CSS --}}
    {{-- <link href="{{ asset('css/mobile-enhancements.css') }}" rel="stylesheet"> --}}
    <style>
        .mobile-card { transition: all 0.2s ease; }
        .mobile-card:hover { transform: translateY(-1px); }
        .mobile-btn-primary { background: linear-gradient(135deg, #ec4899, #be185d); }
        .mobile-progress { background: #e5e7eb; height: 4px; border-radius: 2px; overflow: hidden; }
        .mobile-progress-bar { background: linear-gradient(90deg, #ec4899, #be185d); height: 100%; transition: width 0.3s ease; }
        .touch-target { min-height: 44px; min-width: 44px; }
        .swipe-container { scrollbar-width: none; -ms-overflow-style: none; }
        .swipe-container::-webkit-scrollbar { display: none; }
    </style>
@endpush

@push('scripts')
    <script>
        // Debug Livewire interactions
        document.addEventListener('livewire:load', function () {
            console.log('Livewire loaded');
        });
        
        document.addEventListener('livewire:update', function () {
            console.log('Livewire updated');
        });
        
        // Ensure clicks work after Livewire updates
        document.addEventListener('livewire:update', function () {
            // Re-enable pointer events if they got disabled
            document.querySelectorAll('[wire\\:click]').forEach(function(element) {
                element.style.pointerEvents = 'auto';
            });
        });
    </script>
@endpush

