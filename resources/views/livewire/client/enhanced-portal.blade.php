<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50">
    <!-- Enhanced Client Portal Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-xl font-bold text-pink-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                        <p class="text-sm text-gray-600">Manage your appointments and profile</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ $loyaltyPoints }} Points</div>
                        <div class="text-xs text-gray-500">Loyalty Balance</div>
                    </div>
                    <button class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 13h6V7H4v6zM4 5h6V1H4v4z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="px-4 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <button class="quick-action-card" wire:click="showBookingModal">
                <div class="quick-action-icon bg-pink-100 text-pink-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="font-medium text-gray-900">Book Appointment</div>
                    <div class="text-xs text-gray-500">Schedule new service</div>
                </div>
            </button>

            <button class="quick-action-card" wire:click="showManageBooking">
                <div class="quick-action-icon bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="font-medium text-gray-900">My Appointments</div>
                    <div class="text-xs text-gray-500">View & manage</div>
                </div>
            </button>

            <button class="quick-action-card" wire:click="showProfile">
                <div class="quick-action-icon bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="font-medium text-gray-900">My Profile</div>
                    <div class="text-xs text-gray-500">Update information</div>
                </div>
            </button>

            <button class="quick-action-card" wire:click="showLoyalty">
                <div class="quick-action-icon bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="font-medium text-gray-900">Loyalty Program</div>
                    <div class="text-xs text-gray-500">Rewards & offers</div>
                </div>
            </button>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h2>
                <button class="text-pink-600 hover:text-pink-700 text-sm font-medium" wire:click="showAllAppointments">
                    View All
                </button>
            </div>
            
            @if($upcomingAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="appointment-card">
                            <div class="appointment-date">
                                <div class="date-day">{{ $appointment->appointment_date->format('d') }}</div>
                                <div class="date-month">{{ $appointment->appointment_date->format('M') }}</div>
                            </div>
                            <div class="appointment-details">
                                <div class="appointment-service">{{ $appointment->service->name }}</div>
                                <div class="appointment-time">{{ $appointment->appointment_date->format('g:i A') }}</div>
                                <div class="appointment-staff">with {{ $appointment->staff->user->name }}</div>
                            </div>
                            <div class="appointment-actions">
                                <button class="action-btn action-btn-secondary" wire:click="rescheduleAppointment({{ $appointment->id }})">
                                    Reschedule
                                </button>
                                <button class="action-btn action-btn-primary" wire:click="viewAppointment({{ $appointment->id }})">
                                    View Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming appointments</h3>
                    <p class="mt-1 text-sm text-gray-500">Book your next appointment to get started.</p>
                    <button class="mt-4 bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-sm font-medium" wire:click="showBookingModal">
                        Book Appointment
                    </button>
                </div>
            @endif
        </div>

        <!-- Service History -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Services</h2>
                <button class="text-pink-600 hover:text-pink-700 text-sm font-medium" wire:click="showServiceHistory">
                    View All
                </button>
            </div>
            
            @if($recentServices->count() > 0)
                <div class="space-y-3">
                    @foreach($recentServices as $service)
                        <div class="service-history-item">
                            <div class="service-info">
                                <div class="service-name">{{ $service->service->name }}</div>
                                <div class="service-date">{{ $service->appointment_date->format('M d, Y') }}</div>
                            </div>
                            <div class="service-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $service->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-sm text-gray-500">No service history yet.</p>
                </div>
            @endif
        </div>

        <!-- Loyalty Program -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 mb-6 border border-purple-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Loyalty Program</h2>
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600">{{ $loyaltyPoints }}</div>
                    <div class="text-sm text-gray-600">Points Available</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="loyalty-reward">
                    <div class="reward-icon">🎁</div>
                    <div class="reward-info">
                        <div class="reward-title">Free Service</div>
                        <div class="reward-cost">500 points</div>
                    </div>
                    <button class="reward-btn" wire:click="redeemReward('free_service')">
                        Redeem
                    </button>
                </div>
                
                <div class="loyalty-reward">
                    <div class="reward-icon">💅</div>
                    <div class="reward-info">
                        <div class="reward-title">20% Off</div>
                        <div class="reward-cost">200 points</div>
                    </div>
                    <button class="reward-btn" wire:click="redeemReward('discount')">
                        Redeem
                    </button>
                </div>
                
                <div class="loyalty-reward">
                    <div class="reward-icon">✨</div>
                    <div class="reward-info">
                        <div class="reward-title">Premium Upgrade</div>
                        <div class="reward-cost">1000 points</div>
                    </div>
                    <button class="reward-btn" wire:click="redeemReward('upgrade')">
                        Redeem
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="stat-card">
                <div class="stat-value">{{ $totalAppointments }}</div>
                <div class="stat-label">Total Visits</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">${{ number_format($totalSpent, 0) }}</div>
                <div class="stat-label">Total Spent</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $favoriteService }}</div>
                <div class="stat-label">Favorite Service</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $memberSince }}</div>
                <div class="stat-label">Member Since</div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @if($showBookingModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Book New Appointment</h3>
                        <button wire:click="closeBookingModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                            <select wire:model="selectedService" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select a service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} - ${{ number_format($service->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date</label>
                            <input wire:model="preferredDate" type="date" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                            <select wire:model="preferredTime" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select time</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                            <textarea wire:model="specialRequests" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" placeholder="Any special requests or notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button wire:click="closeBookingModal" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium">
                            Cancel
                        </button>
                        <button wire:click="submitBooking" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white py-2 px-4 rounded-lg font-medium">
                            Book Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.quick-action-card {
    @apply bg-white rounded-lg p-4 shadow-sm border border-gray-200 hover:border-pink-300 hover:shadow-md transition-all cursor-pointer;
}

.quick-action-icon {
    @apply w-12 h-12 rounded-lg flex items-center justify-center mb-3;
}

.quick-action-text {
    @apply text-center;
}

.appointment-card {
    @apply flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200;
}

.appointment-date {
    @apply text-center mr-4;
}

.date-day {
    @apply text-2xl font-bold text-gray-900;
}

.date-month {
    @apply text-sm text-gray-600;
}

.appointment-details {
    @apply flex-1;
}

.appointment-service {
    @apply font-medium text-gray-900;
}

.appointment-time {
    @apply text-sm text-gray-600;
}

.appointment-staff {
    @apply text-sm text-gray-500;
}

.appointment-actions {
    @apply flex space-x-2;
}

.action-btn {
    @apply px-3 py-1 rounded-lg text-sm font-medium transition-colors;
}

.action-btn-primary {
    @apply bg-pink-600 hover:bg-pink-700 text-white;
}

.action-btn-secondary {
    @apply bg-gray-200 hover:bg-gray-300 text-gray-700;
}

.service-history-item {
    @apply flex items-center justify-between p-3 bg-gray-50 rounded-lg;
}

.service-info {
    @apply flex-1;
}

.service-name {
    @apply font-medium text-gray-900;
}

.service-date {
    @apply text-sm text-gray-600;
}

.service-rating {
    @apply flex space-x-1;
}

.loyalty-reward {
    @apply flex items-center p-3 bg-white rounded-lg border border-purple-200;
}

.reward-icon {
    @apply text-2xl mr-3;
}

.reward-info {
    @apply flex-1;
}

.reward-title {
    @apply font-medium text-gray-900;
}

.reward-cost {
    @apply text-sm text-gray-600;
}

.reward-btn {
    @apply bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors;
}

.stat-card {
    @apply bg-white rounded-lg p-4 shadow-sm border border-gray-200 text-center;
}

.stat-value {
    @apply text-2xl font-bold text-gray-900;
}

.stat-label {
    @apply text-sm text-gray-600;
}
</style>
