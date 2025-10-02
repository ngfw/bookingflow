<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Welcome back, {{ auth()->user()->name }}!
                </h1>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Current time: {{ $currentTime }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Today: {{ now()->format('l, F j, Y') }}
                    </div>
                </div>
            </div>
            <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                <a href="{{ route('staff.pos') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m4.5-5a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Open POS
                </a>
                <button wire:click="refreshData" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Appointments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['today']['appointments'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-600">Completed: </span>
                    <span class="font-medium text-green-600">{{ $stats['today']['completed'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($stats['today']['revenue'] ?? 0, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['week']['appointments'] ?? 0 }} appts</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-600">Revenue: </span>
                    <span class="font-medium text-green-600">${{ number_format($stats['week']['revenue'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['month']['appointments'] ?? 0 }} appts</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-gray-600">Revenue: </span>
                    <span class="font-medium text-green-600">${{ number_format($stats['month']['revenue'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Today's Appointments -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Today's Appointments</h3>
                
                @if($todayAppointments && $todayAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($todayAppointments as $appointment)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">
                                                {{ $appointment->client->user->name ?? 'Unknown Client' }}
                                            </h4>
                                            <span class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $appointment->service->name ?? 'Unknown Service' }} 
                                            <span class="text-gray-400">•</span>
                                            {{ $appointment->service->duration_minutes ?? 0 }}min
                                            <span class="text-gray-400">•</span>
                                            ${{ number_format($appointment->service->price ?? 0, 2) }}
                                        </p>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                                @elseif($appointment->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        @if($appointment->status === 'confirmed')
                                            <button wire:click="startAppointment({{ $appointment->id }})" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Start
                                            </button>
                                        @elseif($appointment->status === 'in_progress')
                                            <button wire:click="markAppointmentCompleted({{ $appointment->id }})" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Complete
                                            </button>
                                        @endif
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments today</h3>
                        <p class="mt-1 text-sm text-gray-500">Enjoy your free time!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Next Appointment -->
            @if($stats['today']['next_appointment'] ?? null)
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-green-800 mb-3">Next Appointment</h3>
                    <div class="text-green-700">
                        <p class="font-medium">{{ $stats['today']['next_appointment']->client->user->name ?? 'Unknown Client' }}</p>
                        <p class="text-sm">{{ $stats['today']['next_appointment']->service->name ?? 'Unknown Service' }}</p>
                        <p class="text-sm font-medium mt-2">
                            {{ \Carbon\Carbon::parse($stats['today']['next_appointment']->appointment_date)->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Today's Schedule -->
            @if($todaysSchedule)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Today's Schedule</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Start Time:</span>
                                <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($todaysSchedule->start_time)->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">End Time:</span>
                                <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($todaysSchedule->end_time)->format('g:i A') }}</span>
                            </div>
                            @if($todaysSchedule->break_start && $todaysSchedule->break_end)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Break:</span>
                                    <span class="text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($todaysSchedule->break_start)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::parse($todaysSchedule->break_end)->format('g:i A') }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium capitalize">{{ $todaysSchedule->status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">No Schedule Today</h3>
                    <p class="text-sm text-yellow-700">You don't have a schedule set for today.</p>
                </div>
            @endif

            <!-- Quick Notifications -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notifications</h3>
                    <div class="space-y-3">
                        @foreach($notifications as $notification)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @if($notification['type'] === 'success')
                                        <div class="w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                                    @elseif($notification['type'] === 'warning')
                                        <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2"></div>
                                    @else
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $notification['message'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $notification['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    @if($upcomingAppointments && $upcomingAppointments->count() > 0)
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upcoming Appointments</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-medium text-gray-900">
                                    {{ $appointment->client->user->name ?? 'Unknown Client' }}
                                </h4>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $appointment->service->name ?? 'Unknown Service' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>