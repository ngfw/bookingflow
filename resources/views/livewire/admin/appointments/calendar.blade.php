<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Appointment Calendar</h1>
                <p class="text-gray-600">View and manage appointments in calendar format</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.appointments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    List View
                </a>
                <a href="{{ route('admin.appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Book Appointment
                </a>
            </div>
        </div>
    </div>

    <!-- Calendar Controls -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- View Mode Toggle -->
                <div class="flex space-x-2">
                    <button wire:click="changeViewMode('month')" 
                            class="px-4 py-2 text-sm font-medium rounded-md {{ $viewMode === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Month
                    </button>
                    <button wire:click="changeViewMode('week')" 
                            class="px-4 py-2 text-sm font-medium rounded-md {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Week
                    </button>
                    <button wire:click="changeViewMode('day')" 
                            class="px-4 py-2 text-sm font-medium rounded-md {{ $viewMode === 'day' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Day
                    </button>
                </div>

                <!-- Navigation -->
                <div class="flex items-center space-x-4">
                    <button wire:click="previousPeriod" class="p-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <h2 class="text-xl font-semibold text-gray-900">
                        @if($viewMode === 'month')
                            {{ $currentDate->format('F Y') }}
                        @elseif($viewMode === 'week')
                            {{ $currentDate->startOfWeek()->format('M j') }} - {{ $currentDate->endOfWeek()->format('M j, Y') }}
                        @else
                            {{ $currentDate->format('l, F j, Y') }}
                        @endif
                    </h2>
                    
                    <button wire:click="nextPeriod" class="p-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <button wire:click="goToToday" class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800">
                        Today
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex space-x-4">
                    <select wire:model.live="staffFilter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                    
                    <select wire:model.live="serviceFilter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($viewMode === 'month')
            <!-- Month View -->
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                <!-- Days of Week Header -->
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sun</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mon</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tue</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Wed</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thu</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fri</div>
                <div class="bg-gray-50 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sat</div>

                <!-- Calendar Days -->
                @foreach($calendarDays as $day)
                    <div class="bg-white min-h-32 p-2 {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }} {{ $day['isToday'] ? 'bg-blue-50' : '' }} {{ $day['isSelected'] ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }} {{ $day['isToday'] ? 'text-blue-600 font-bold' : '' }}">
                                {{ $day['day'] }}
                            </span>
                            @if($day['isCurrentMonth'])
                                <button wire:click="selectDate('{{ $day['date'] }}')" class="text-xs text-blue-600 hover:text-blue-800">
                                    View
                                </button>
                            @endif
                        </div>
                        
                        <div class="space-y-1">
                            @foreach($this->getAppointmentsForDate($day['date']) as $appointment)
                                <div class="text-xs p-1 rounded truncate cursor-pointer hover:bg-gray-100
                                    @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($appointment->status === 'no_show') bg-gray-100 text-gray-800
                                    @endif">
                                    <div class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</div>
                                    <div class="truncate">{{ $appointment->client->user->name }}</div>
                                    <div class="truncate">{{ $appointment->service->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($viewMode === 'week')
            <!-- Week View -->
            <div class="grid grid-cols-7 gap-px bg-gray-200">
                @foreach($calendarDays as $day)
                    <div class="bg-white min-h-96 p-4 {{ $day['isToday'] ? 'bg-blue-50' : '' }} {{ $day['isSelected'] ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="text-center mb-4">
                            <div class="text-sm font-medium text-gray-500">{{ Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                            <div class="text-lg font-semibold {{ $day['isToday'] ? 'text-blue-600' : 'text-gray-900' }}">{{ $day['day'] }}</div>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($this->getAppointmentsForDate($day['date']) as $appointment)
                                <div class="text-xs p-2 rounded border-l-4 cursor-pointer hover:bg-gray-50
                                    @if($appointment->status === 'pending') border-yellow-400 bg-yellow-50
                                    @elseif($appointment->status === 'confirmed') border-green-400 bg-green-50
                                    @elseif($appointment->status === 'completed') border-blue-400 bg-blue-50
                                    @elseif($appointment->status === 'cancelled') border-red-400 bg-red-50
                                    @elseif($appointment->status === 'no_show') border-gray-400 bg-gray-50
                                    @endif">
                                    <div class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</div>
                                    <div class="font-semibold">{{ $appointment->client->user->name }}</div>
                                    <div class="text-gray-600">{{ $appointment->service->name }}</div>
                                    <div class="text-gray-500">{{ $appointment->staff->user->name ?? 'Unassigned' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Day View -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $currentDate->format('l, F j, Y') }}</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($this->getAppointmentsForDate($currentDate->format('Y-m-d')) as $appointment)
                        <div class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($appointment->status === 'no_show') bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="font-semibold text-gray-900">{{ $appointment->client->user->name }}</div>
                                <div class="text-sm text-gray-600">{{ $appointment->service->name }}</div>
                                <div class="text-sm text-gray-500">Staff: {{ $appointment->staff->user->name ?? 'Unassigned' }}</div>
                                <div class="text-sm text-gray-500">Duration: {{ $appointment->service->duration_minutes }} min</div>
                                <div class="text-sm font-medium text-gray-900">${{ number_format($appointment->service->price, 2) }}</div>
                            </div>
                            
                            @if($appointment->notes)
                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                    {{ $appointment->notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($this->getAppointmentsForDate($currentDate->format('Y-m-d'))->isEmpty())
                        <div class="col-span-full text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments</h3>
                            <p class="mt-1 text-sm text-gray-500">No appointments scheduled for this day.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
