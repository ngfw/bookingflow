<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">My Appointments</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your scheduled appointments and track their status.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Clients</label>
                <input wire:model.live="search" type="text" id="search" 
                       placeholder="Search by name or email..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Date Filter -->
            <div>
                <label for="selectedDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input wire:model.live="selectedDate" type="date" id="selectedDate"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No Show</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($appointments->count() > 0)
                <div class="space-y-4">
                    @foreach($appointments as $appointment)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-lg font-medium text-gray-900">
                                            {{ $appointment->client->user->name ?? 'Unknown Client' }}
                                        </h4>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y \a\t g:i A') }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                        <div>
                                            <span class="font-medium">Service:</span>
                                            {{ $appointment->service->name ?? 'Unknown Service' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Duration:</span>
                                            {{ $appointment->service->duration_minutes ?? 0 }} minutes
                                        </div>
                                        <div>
                                            <span class="font-medium">Price:</span>
                                            ${{ number_format($appointment->service->price ?? 0, 2) }}
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($appointment->status === 'pending') bg-gray-100 text-gray-800
                                            @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($appointment->status === 'checked_in') bg-indigo-100 text-indigo-800
                                            @elseif($appointment->status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($appointment->status === 'no_show') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>

                                        <div class="flex space-x-2">
                                            @if($appointment->status === 'confirmed' || $appointment->status === 'checked_in')
                                                <button wire:click="startAppointment({{ $appointment->id }})" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Start
                                                </button>
                                            @endif

                                            @if($appointment->status === 'in_progress')
                                                <button wire:click="completeAppointment({{ $appointment->id }})" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Complete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($appointment->notes)
                                <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Notes:</span> {{ $appointment->notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments found</h3>
                    <p class="mt-1 text-sm text-gray-500">No appointments match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>