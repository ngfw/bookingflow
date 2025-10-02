<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Staff Schedules</h1>
                <p class="text-gray-600">Manage staff working hours and availability</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.staff.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Back to Staff
                </a>
                <button wire:click="addSchedule" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Add Schedule
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Staff</label>
                    <select wire:model.live="selectedStaff" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Staff</option>
                        @foreach($staff as $staffMember)
                            <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input wire:model.live="selectedDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Calendar View -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">
                Schedule for {{ $selectedDate ? Carbon\Carbon::parse($selectedDate)->format('F j, Y') : 'Today' }}
            </h2>
            
            @if($schedules->count() > 0)
                <div class="space-y-4">
                    @foreach($schedules as $schedule)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    {{ $schedule->staff->user->name ?? 'Unknown Staff' }}
                                                </h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($schedule->status === 'available') bg-green-100 text-green-800 
                                                    @elseif($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                                    @elseif($schedule->status === 'sick') bg-yellow-100 text-yellow-800
                                                    @elseif($schedule->status === 'vacation') bg-purple-100 text-purple-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                <span class="font-medium">{{ Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}</span>
                                                <span class="mx-2">-</span>
                                                <span class="font-medium">{{ Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                                                @if($schedule->break_start && $schedule->break_end)
                                                    <span class="ml-4 text-gray-400">
                                                        (Break: {{ Carbon\Carbon::parse($schedule->break_start)->format('g:i A') }} - {{ Carbon\Carbon::parse($schedule->break_end)->format('g:i A') }})
                                                    </span>
                                                @endif
                                            </div>
                                            @if($schedule->notes)
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <span class="font-medium">Notes:</span> {{ $schedule->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="editSchedule({{ $schedule->id }})" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button wire:click="deleteSchedule({{ $schedule->id }})" 
                                            wire:confirm="Are you sure you want to delete this schedule?"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($selectedStaff)
                            No schedules found for the selected staff member on this date.
                        @else
                            No schedules found for this date. Select a staff member to add schedules.
                        @endif
                    </p>
                    @if($selectedStaff)
                        <div class="mt-6">
                            <button wire:click="addSchedule" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Add Schedule
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Schedule Modal -->
    @if($showScheduleModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingSchedule ? 'Edit Schedule' : 'Add New Schedule' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveSchedule" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                                <select wire:model="formStaffId" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('formStaffId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input wire:model="formDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input wire:model="formStartTime" type="time" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formStartTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input wire:model="formEndTime" type="time" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formEndTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Break Start</label>
                                <input wire:model="formBreakStart" type="time" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formBreakStart') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Break End</label>
                                <input wire:model="formBreakEnd" type="time" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('formBreakEnd') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model="formStatus" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="available">Available</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="unavailable">Unavailable</option>
                                    <option value="sick">Sick</option>
                                    <option value="vacation">Vacation</option>
                                </select>
                                @error('formStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Recurring</label>
                                <div class="flex items-center">
                                    <input wire:model="formIsRecurring" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Make this schedule recurring</span>
                                </div>
                            </div>
                        </div>

                        @if($formIsRecurring)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Recurring Type</label>
                                    <select wire:model="formRecurringType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    @error('formRecurringType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input wire:model="formRecurringEndDate" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('formRecurringEndDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea wire:model="formNotes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional notes about this schedule..."></textarea>
                            @error('formNotes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                {{ $editingSchedule ? 'Update Schedule' : 'Create Schedule' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
