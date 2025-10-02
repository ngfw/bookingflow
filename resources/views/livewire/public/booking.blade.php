<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Book Your Appointment</h1>
            <p class="text-xl text-gray-600">Schedule your beauty treatment in just a few simple steps</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $i <= $currentStep ? 'bg-pink-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $i }}
                        </div>
                        @if($i < $totalSteps)
                            <div class="w-16 h-1 {{ $i < $currentStep ? 'bg-pink-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
            <div class="flex justify-center mt-4">
                <div class="text-sm text-gray-600">
                    @if($currentStep === 1) Choose Service
                    @elseif($currentStep === 2) Select Date & Time
                    @elseif($currentStep === 3) Your Information
                    @elseif($currentStep === 4) Confirmation
                    @endif
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($currentStep === 1)
                <!-- Step 1: Service Selection -->
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Choose Your Service</h2>
                    
                    <!-- Category Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Service Category</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <button wire:click="$set('selectedCategory', '')" 
                                    class="p-3 rounded-lg border-2 {{ !$selectedCategory ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }} transition-colors">
                                All Services
                            </button>
                            @foreach($categories as $category)
                                <button wire:click="$set('selectedCategory', '{{ $category->id }}')" 
                                        class="p-3 rounded-lg border-2 {{ $selectedCategory == $category->id ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }} transition-colors">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($services as $service)
                            <div wire:click="$set('selectedService', '{{ $service->id }}')" 
                                 class="p-6 rounded-xl border-2 cursor-pointer transition-all hover:shadow-lg {{ $selectedService == $service->id ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $service->name }}</h3>
                                    <span class="text-lg font-bold text-pink-600">${{ number_format($service->price, 2) }}</span>
                                </div>
                                <p class="text-gray-600 mb-3">{{ Str::limit($service->description, 100) }}</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $service->duration_minutes }} minutes
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($selectedService)
                        <div class="mt-6 flex justify-end">
                            <button wire:click="nextStep" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                Continue to Date & Time
                            </button>
                        </div>
                    @endif
                </div>

            @elseif($currentStep === 2)
                <!-- Step 2: Date & Time Selection -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Select Date & Time</h2>
                        <button wire:click="previousStep" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>

                    @if($selectedService)
                        @php $service = \App\Models\Service::find($selectedService) @endphp
                        <div class="bg-pink-50 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                            <p class="text-sm text-gray-600">${{ number_format($service->price, 2) }} • {{ $service->duration_minutes }} minutes</p>
                        </div>
                    @endif

                    <!-- Date Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Available Dates</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($availableDates as $date)
                                <button wire:click="selectDate('{{ $date['date'] }}')" 
                                        class="p-3 rounded-lg border-2 text-center transition-colors {{ $selectedDate == $date['date'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <div class="text-sm font-medium">{{ $date['day'] }}</div>
                                    <div class="text-xs text-gray-600">{{ $date['display'] }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Time Selection -->
                    @if($selectedDate)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Available Times</label>
                            @if(count($availableTimeSlots) > 0)
                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach($availableTimeSlots as $slot)
                                        <button wire:click="selectTimeSlot({{ json_encode($slot) }})" 
                                                class="p-3 rounded-lg border-2 text-center transition-colors {{ $selectedTime == $slot['time'] ? 'border-pink-500 bg-pink-50' : 'border-gray-200 hover:border-gray-300' }}">
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

                    @if($selectedTime)
                        <div class="mt-6 flex justify-end">
                            <button wire:click="nextStep" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                Continue to Information
                            </button>
                        </div>
                    @endif
                </div>

            @elseif($currentStep === 3)
                <!-- Step 3: Client Information -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Your Information</h2>
                        <button wire:click="previousStep" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="confirmBooking" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input wire:model="clientName" type="text" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('clientName') border-red-300 @enderror">
                                @error('clientName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input wire:model="clientEmail" type="email" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('clientEmail') border-red-300 @enderror">
                                @error('clientEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input wire:model="clientPhone" type="tel" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('clientPhone') border-red-300 @enderror">
                                @error('clientPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                                <textarea wire:model="clientNotes" rows="3" 
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('clientNotes') border-red-300 @enderror" 
                                          placeholder="Any special requests or notes..."></textarea>
                                @error('clientNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Appointment Summary -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Summary</h3>
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
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-medium">{{ \App\Models\Service::find($selectedService)->duration_minutes }} minutes</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-pink-600">${{ number_format(\App\Models\Service::find($selectedService)->price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>

            @elseif($currentStep === 4)
                <!-- Step 4: Confirmation -->
                <div class="p-8 text-center">
                    @if($bookingConfirmed)
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">Booking Confirmed!</h2>
                            <p class="text-lg text-gray-600 mb-6">Your appointment has been successfully booked.</p>
                            
                            <div class="bg-pink-50 rounded-lg p-6 max-w-md mx-auto">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h3>
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
                            
                            <div class="mt-8 space-y-4">
                                <p class="text-gray-600">You will receive a confirmation email shortly.</p>
                                <div class="flex justify-center space-x-4">
                                    <a href="/" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Back to Home
                                    </a>
                                    <button wire:click="$set('currentStep', 1)" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                        Book Another
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Processing Your Booking...</h2>
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-600 mx-auto"></div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Flash Messages -->
        @if (session()->has('error'))
            <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>