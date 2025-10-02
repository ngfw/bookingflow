<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Staff Schedules</h1>
                    <p class="text-sm text-gray-600">Manage working hours</p>
                </div>
                <button wire:click="addSchedule" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Add Schedule
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Content -->
    <div class="px-4 py-6">
        <!-- Mobile Filters -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="p-4">
                <!-- Staff Selector -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                    <div class="relative">
                        <button wire:click="toggleStaffSelector" 
                                class="w-full text-left px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @if($selectedStaff)
                                {{ $staff->find($selectedStaff)->user->name ?? 'Select Staff' }}
                            @else
                                All Staff
                            @endif
                        </button>
                        @if($showStaffSelector)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-20">
                                <button wire:click="$set('selectedStaff', '')" 
                                        class="w-full text-left px-4 py-2 hover:bg-gray-50 {{ !$selectedStaff ? 'bg-blue-50' : '' }}">
                                    All Staff
                                </button>
                                @foreach($staff as $staffMember)
                                    <button wire:click="$set('selectedStaff', '{{ $staffMember->id }}')" 
                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 {{ $selectedStaff == $staffMember->id ? 'bg-blue-50' : '' }}">
                                        {{ $staffMember->user->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Date Selector -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <div class="relative">
                        <button wire:click="toggleDatePicker" 
                                class="w-full text-left px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            {{ Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                        </button>
                        @if($showDatePicker)
                            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-20">
                                <!-- Quick Date Options -->
                                <div class="p-3 border-b border-gray-200">
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($quickDateOptions as $option)
                                            <button wire:click="selectQuickDate('{{ $option['date'] }}')" 
                                                    class="px-3 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50">
                                                {{ $option['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Custom Date Input -->
                                <div class="p-3">
                                    <input wire:model.live="selectedDate" type="date" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule List -->
        <div class="space-y-4">
            @if($schedules->count() > 0)
                @foreach($schedules as $schedule)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $schedule->staff->user->name ?? 'Unknown Staff' }}
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($schedule->status === 'available') bg-green-100 text-green-800 
                                            @elseif($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($schedule->status === 'sick') bg-yellow-100 text-yellow-800
                                            @elseif($schedule->status === 'vacation') bg-purple-100 text-purple-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - {{ Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                    </div>

                                    @if($schedule->break_start && $schedule->break_end)
                                        <div class="flex items-center text-sm text-gray-500 mb-2">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                            </svg>
                                            Break: {{ Carbon\Carbon::parse($schedule->break_start)->format('g:i A') }} - {{ Carbon\Carbon::parse($schedule->break_end)->format('g:i A') }}
                                        </div>
                                    @endif

                                    @if($schedule->notes)
                                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                            <p class="text-sm text-gray-700">{{ $schedule->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <button wire:click="editSchedule({{ $schedule->id }})" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                                    Edit
                                </button>
                                <button wire:click="duplicateSchedule({{ $schedule->id }})" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                                    Duplicate
                                </button>
                                <button wire:click="deleteSchedule({{ $schedule->id }})" 
                                        wire:confirm="Are you sure you want to delete this schedule?"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No schedules found</h3>
                    <p class="text-gray-600 mb-6">
                        @if($selectedStaff)
                            No schedules found for the selected staff member on this date.
                        @else
                            No schedules found for this date.
                        @endif
                    </p>
                    <button wire:click="addSchedule" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        Add Schedule
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile Schedule Modal -->
    @if($showScheduleModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
            <div class="bg-white rounded-t-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $editingSchedule ? 'Edit Schedule' : 'Add Schedule' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveSchedule" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Staff Member</label>
                            <select wire:model="formStaffId" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                <option value="">Select Staff</option>
                                @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}">{{ $staffMember->user->name }}</option>
                                @endforeach
                            </select>
                            @error('formStaffId') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input wire:model="formDate" type="date" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                            @error('formDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                <input wire:model="formStartTime" type="time" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('formStartTime') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                <input wire:model="formEndTime" type="time" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('formEndTime') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Break Start</label>
                                <input wire:model="formBreakStart" type="time" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('formBreakStart') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Break End</label>
                                <input wire:model="formBreakEnd" type="time" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                @error('formBreakEnd') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select wire:model="formStatus" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4">
                                <option value="available">Available</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="unavailable">Unavailable</option>
                                <option value="sick">Sick</option>
                                <option value="vacation">Vacation</option>
                            </select>
                            @error('formStatus') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea wire:model="formNotes" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base py-3 px-4" 
                                      placeholder="Optional notes..."></textarea>
                            @error('formNotes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button type="button" wire:click="closeModal" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium">
                                {{ $editingSchedule ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 left-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 left-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>

