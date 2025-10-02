<div class="p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Appointment Reminder Automation</h1>
                <p class="text-gray-600">Manage automated appointment reminders and scheduling</p>
            </div>
            <div class="flex space-x-4">
                <button 
                    wire:click="createSchedule" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Schedule
                </button>
                <button 
                    wire:click="runDryRun" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Dry Run
                </button>
                <button 
                    wire:click="runReminderCommand" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Send Reminders
                </button>
                <a href="{{ route('notifications.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    All Notifications
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today']['sent'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['today']['delivered'] }} delivered</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['week']['sent'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['week']['delivered'] }} delivered</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['month']['sent'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['month']['delivered'] }} delivered</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Upcoming</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $upcomingReminders->count() }}</p>
                    <p class="text-xs text-gray-500">Next 24 hours</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Reminder Schedules -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Reminder Schedules</h3>
                    <button 
                        wire:click="createSchedule" 
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                        + Add Schedule
                    </button>
                </div>
            </div>
            <div class="p-6">
                <!-- Search -->
                <div class="mb-4">
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Search schedules..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Schedules List -->
                <div class="space-y-4">
                    @forelse($schedules as $schedule)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-900">{{ $schedule->name }}</h4>
                                        @if($schedule->is_default)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Default</span>
                                        @endif
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($schedule->is_active) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $schedule->description }}</p>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <p><strong>{{ $schedule->hours_before }} hours</strong> before appointment</p>
                                        <p><strong>Types:</strong> {{ $schedule->getNotificationTypesText() }}</p>
                                        @if($schedule->conditions)
                                            <p><strong>Conditions:</strong> {{ $schedule->getConditionsText() }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button 
                                        wire:click="editSchedule({{ $schedule->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="toggleScheduleStatus({{ $schedule->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900 text-sm"
                                    >
                                        {{ $schedule->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    @if(!$schedule->is_default)
                                        <button 
                                            wire:click="deleteSchedule({{ $schedule->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('Are you sure?')"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No reminder schedules</h3>
                            <p class="mt-1 text-sm text-gray-500">Create a schedule to start sending automated reminders.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h3>
                <p class="text-sm text-gray-600">Next 24 hours</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($upcomingReminders as $appointment)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $appointment->client->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $appointment->service->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time->format('g:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-500">Staff: {{ $appointment->staff->name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $appointment->appointment_date->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming appointments</h3>
                            <p class="mt-1 text-sm text-gray-500">No appointments scheduled for the next 24 hours.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    @if($showScheduleModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $selectedSchedule ? 'Edit Schedule' : 'Create Schedule' }}
                        </h3>
                        <button wire:click="closeScheduleModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="storeSchedule">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hours Before *</label>
                                <input 
                                    type="number" 
                                    wire:model="hours_before" 
                                    min="1"
                                    max="8760"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea 
                                wire:model="description" 
                                rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            ></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notification Types *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="notification_types" 
                                        value="email"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Email</span>
                                </label>
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="notification_types" 
                                        value="sms"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">SMS</span>
                                </label>
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="notification_types" 
                                        value="push"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Push Notification</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_active" 
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_default" 
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Default Schedule</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button 
                                type="button" 
                                wire:click="closeScheduleModal" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium"
                            >
                                {{ $selectedSchedule ? 'Update' : 'Create' }} Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
