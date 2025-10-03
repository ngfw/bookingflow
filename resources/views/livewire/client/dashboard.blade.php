<div class="min-h-screen bg-gray-50">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-pink-100">Here's what's happening with your appointments and rewards</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Loyalty Points -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Loyalty Points</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($loyaltyPoints) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($totalSpent, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Visit Count -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-pink-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Visits</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($visitCount) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Appointments -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('booking') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 transition">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Book Appointment
                        </a>
                        <a href="{{ route('services') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Services
                        </a>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Appointments</h2>
                    @if($upcomingAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingAppointments as $appointment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $appointment->service->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $appointment->appointment_date->format('F d, Y \a\t g:i A') }}
                                            </p>
                                            @if($appointment->staff)
                                                <p class="text-sm text-gray-600">
                                                    <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    with {{ $appointment->staff->user->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No upcoming appointments</p>
                            <a href="{{ route('booking') }}" class="mt-4 inline-flex items-center text-sm font-medium text-pink-600 hover:text-pink-700">
                                Book your first appointment
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Appointment History -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Visits</h2>
                    @if($recentAppointments->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentAppointments as $appointment)
                                <div class="border-l-4 border-pink-500 pl-4 py-2">
                                    <h3 class="font-medium text-gray-900">{{ $appointment->service->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('F d, Y') }}</p>
                                    @if($appointment->staff)
                                        <p class="text-sm text-gray-500">{{ $appointment->staff->user->name }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No visit history yet</p>
                    @endif
                </div>
            </div>

            <!-- Right Column - Payments & Recommendations -->
            <div class="space-y-6">
                <!-- Recent Payments -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h2>
                    @if($recentPayments->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentPayments as $payment)
                                <div class="border border-gray-200 rounded p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($payment->payment_method) }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No payment history</p>
                    @endif
                </div>

                <!-- Recommended Services -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Recommended for You</h2>
                    @if($recommendedServices->count() > 0)
                        <div class="space-y-4">
                            @foreach($recommendedServices as $service)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition">
                                    <h3 class="font-medium text-gray-900">{{ $service->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($service->description ?? '', 60) }}</p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-pink-600 font-semibold">${{ number_format($service->price, 2) }}</span>
                                        <a href="{{ route('booking') }}" class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                                            Book Now →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">Check out our services</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
