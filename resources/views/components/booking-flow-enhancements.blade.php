{{-- Booking Flow Enhancements Component --}}

{{-- Smart Service Recommendations --}}
@if(isset($recommendedServices) && $recommendedServices->count() > 0)
    <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-4 mb-6 border border-pink-200">
        <div class="flex items-center mb-3">
            <svg class="w-5 h-5 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Recommended for You</h3>
        </div>
        <p class="text-sm text-gray-600 mb-4">Based on your preferences and popular choices</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($recommendedServices as $service)
                <div class="bg-white rounded-lg p-3 border border-pink-200 hover:border-pink-300 transition-colors cursor-pointer"
                     wire:click="$set('selectedService', '{{ $service->id }}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $service->name }}</h4>
                            <p class="text-sm text-gray-600">${{ number_format($service->price, 2) }} • {{ $service->duration_minutes }}min</p>
                        </div>
                        <div class="text-pink-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Quick Booking Options --}}
<div class="bg-white rounded-xl p-4 mb-6 shadow-sm border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Book</h3>
    <div class="grid grid-cols-2 gap-3">
        <button class="p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 transition-colors text-left"
                wire:click="quickBook('express')">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="font-medium text-gray-900">Express</span>
            </div>
            <p class="text-sm text-gray-600">30 min • $45</p>
        </button>
        <button class="p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 transition-colors text-left"
                wire:click="quickBook('premium')">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                <span class="font-medium text-gray-900">Premium</span>
            </div>
            <p class="text-sm text-gray-600">90 min • $120</p>
        </button>
    </div>
</div>

{{-- Availability Status --}}
@if(isset($nextAvailableSlot))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-blue-900">Next Available</h4>
                <p class="text-sm text-blue-700">{{ $nextAvailableSlot['date'] }} at {{ $nextAvailableSlot['time'] }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Booking Tips --}}
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h4 class="font-medium text-yellow-900 mb-2">Booking Tips</h4>
            <ul class="text-sm text-yellow-800 space-y-1">
                <li>• Book in advance for popular time slots</li>
                <li>• Arrive 10 minutes early for your appointment</li>
                <li>• Cancel or reschedule at least 24 hours in advance</li>
            </ul>
        </div>
    </div>
</div>

{{-- Loyalty Points Display --}}
@if(isset($loyaltyPoints) && $loyaltyPoints > 0)
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-6 border border-purple-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-purple-900">Loyalty Points</h4>
                    <p class="text-sm text-purple-700">You have {{ $loyaltyPoints }} points</p>
                </div>
            </div>
            <button class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                Redeem
            </button>
        </div>
    </div>
@endif

{{-- Service Package Deals --}}
@if(isset($packageDeals) && $packageDeals->count() > 0)
    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 mb-6 border border-green-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Special Packages</h3>
        <div class="space-y-3">
            @foreach($packageDeals as $deal)
                <div class="bg-white rounded-lg p-3 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $deal['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $deal['description'] }}</p>
                            <p class="text-sm text-green-600 font-medium">Save ${{ $deal['savings'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">${{ number_format($deal['price'], 2) }}</p>
                            <button class="text-green-600 hover:text-green-700 text-sm font-medium">
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Staff Availability Indicator --}}
@if(isset($staffAvailability))
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Staff Availability</h3>
        <div class="space-y-3">
            @foreach($staffAvailability as $staff)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-sm font-medium text-pink-600">{{ substr($staff['name'], 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $staff['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $staff['specialty'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full {{ $staff['available'] ? 'bg-green-400' : 'bg-red-400' }} mr-2"></div>
                            <span class="text-sm {{ $staff['available'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $staff['available'] ? 'Available' : 'Busy' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Booking Confirmation Enhancements --}}
@if(isset($bookingConfirmed) && $bookingConfirmed)
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center mb-3">
            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-green-900">Booking Confirmed!</h3>
        </div>
        <div class="space-y-2 text-sm text-green-800">
            <p>• Confirmation email sent to your inbox</p>
            <p>• SMS reminder will be sent 24 hours before</p>
            <p>• Add to calendar option available</p>
        </div>
        <div class="mt-4 flex space-x-3">
            <button class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Add to Calendar
            </button>
            <button class="flex-1 bg-white hover:bg-gray-50 text-green-600 border border-green-600 py-2 px-4 rounded-lg text-sm font-medium">
                Share
            </button>
        </div>
    </div>
@endif
