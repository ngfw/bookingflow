<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.appointments.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Book New Appointment</h1>
                <p class="text-gray-600">Schedule a new appointment with time slot availability checking</p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Appointment Details -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Appointment Details</h2>
                    
                    <div class="space-y-4">
                        <!-- Client Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                            <select wire:model="client_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('client_id') border-red-300 @enderror">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->user->name }} ({{ $client->user->email }})</option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Service Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service *</label>
                            <select wire:model="service_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('service_id') border-red-300 @enderror">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} - ${{ number_format($service->price, 2) }} ({{ $service->duration_minutes }} min)</option>
                                @endforeach
                            </select>
                            @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Staff Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                            <select wire:model="staff_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('staff_id') border-red-300 @enderror">
                                <option value="">Auto-assign (first available)</option>
                                @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }} - {{ $staffMember->specialization }}</option>
                                @endforeach
                            </select>
                            @error('staff_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Appointment Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Appointment Date *</label>
                            <input wire:model="appointment_date" type="date" min="{{ date('Y-m-d') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('appointment_date') border-red-300 @enderror">
                            @error('appointment_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Appointment Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select wire:model="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea wire:model="notes" rows="3" 
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror" 
                                      placeholder="Any special notes or instructions..."></textarea>
                            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Recurring Appointment Options -->
                        <div class="border-t pt-4">
                            <div class="flex items-center mb-4">
                                <input wire:model="is_recurring" type="checkbox" id="is_recurring" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_recurring" class="ml-2 block text-sm font-medium text-gray-900">
                                    Make this a recurring appointment
                                </label>
                            </div>

                            @if($is_recurring)
                                <div class="space-y-4">
                                    <div>
                                        <label for="recurring_pattern" class="block text-sm font-medium text-gray-700 mb-2">Recurring Pattern *</label>
                                        <select wire:model="recurring_pattern" id="recurring_pattern" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('recurring_pattern') border-red-300 @enderror">
                                            <option value="">Select Pattern</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="biweekly">Bi-weekly (Every 2 weeks)</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                        @error('recurring_pattern') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="recurring_end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                                        <input wire:model="recurring_end_date" type="date" id="recurring_end_date" min="{{ Carbon\Carbon::parse($appointment_date)->addDay()->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('recurring_end_date') border-red-300 @enderror">
                                        @error('recurring_end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">Recurring Appointment Information</h3>
                                                <div class="mt-1 text-sm text-blue-700">
                                                    <p>Recurring appointments will be created automatically based on the selected pattern and end date. Each appointment will be checked for staff availability and conflicts.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Slot Selection -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Available Time Slots</h2>
                    
                    @if($service_id && $appointment_date)
                        @if(count($availableTimeSlots) > 0)
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($availableTimeSlots as $slot)
                                    <label class="relative">
                                        <input wire:model="appointment_time" type="radio" value="{{ $slot['time'] }}" 
                                               class="sr-only peer">
                                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-colors">
                                            <div class="text-sm font-medium text-gray-900">{{ $slot['display'] }}</div>
                                            <div class="text-xs text-gray-500">Ends at {{ $slot['end_time'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            @if($appointment_time)
                                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-green-800">
                                            Time slot selected: {{ Carbon\Carbon::parse($appointment_time)->format('g:i A') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No Available Time Slots</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($staff_id)
                                        No available time slots for the selected staff member on this date.
                                    @else
                                        No staff members available for this service on this date.
                                    @endif
                                </p>
                                <p class="mt-2 text-sm text-gray-500">Try selecting a different date or staff member.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Select Service and Date</h3>
                            <p class="mt-1 text-sm text-gray-500">Please select a service and appointment date to view available time slots.</p>
                        </div>
                    @endif
                </div>

                <!-- Service Information -->
                @if($service_id)
                    @php $selectedService = $services->firstWhere('id', $service_id) @endphp
                    @if($selectedService)
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Service Information</h3>
                            <div class="space-y-2 text-sm">
                                <div><span class="font-medium">Duration:</span> {{ $selectedService->duration_minutes }} minutes</div>
                                <div><span class="font-medium">Price:</span> ${{ number_format($selectedService->price, 2) }}</div>
                                @if($selectedService->buffer_time_minutes)
                                    <div><span class="font-medium">Buffer Time:</span> {{ $selectedService->buffer_time_minutes }} minutes</div>
                                @endif
                                @if($selectedService->preparation_instructions)
                                    <div><span class="font-medium">Preparation:</span> {{ $selectedService->preparation_instructions }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.appointments.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    @if(!$appointment_time) disabled @endif
                    class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                Book Appointment
            </button>
        </div>
    </form>

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
</div>