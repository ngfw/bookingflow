<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Salon Settings</h1>
            <p class="text-gray-600">Configure your salon's general and booking settings.</p>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="saveSettings" class="space-y-8">
            <!-- General Settings -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">General Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="salon_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Salon Name *
                        </label>
                        <input wire:model="salon_name" type="text" id="salon_name"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('salon_name') border-red-300 @enderror">
                        @error('salon_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="salon_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <input wire:model="salon_description" type="text" id="salon_description"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>
                </div>
            </div>

            <!-- Theme Colors -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Theme Colors</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Primary Color *
                        </label>
                        <div class="flex items-center space-x-3">
                            <input wire:model="primary_color" type="color" id="primary_color"
                                   class="h-10 w-20 border-gray-300 rounded-lg">
                            <input wire:model="primary_color" type="text"
                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        @error('primary_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Secondary Color *
                        </label>
                        <div class="flex items-center space-x-3">
                            <input wire:model="secondary_color" type="color" id="secondary_color"
                                   class="h-10 w-20 border-gray-300 rounded-lg">
                            <input wire:model="secondary_color" type="text"
                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        @error('secondary_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Accent Color *
                        </label>
                        <div class="flex items-center space-x-3">
                            <input wire:model="accent_color" type="color" id="accent_color"
                                   class="h-10 w-20 border-gray-300 rounded-lg">
                            <input wire:model="accent_color" type="text"
                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        @error('accent_color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Booking Settings -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Maximum Booking Days -->
                    <div>
                        <label for="max_booking_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Booking Period *
                        </label>
                        <select wire:model="max_booking_days" id="max_booking_days"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('max_booking_days') border-red-300 @enderror">
                            @foreach($bookingDaysOptions as $days => $label)
                                <option value="{{ $days }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">How far in advance customers can book appointments</p>
                        @error('max_booking_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Minimum Booking Hours -->
                    <div>
                        <label for="min_booking_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Advance Hours *
                        </label>
                        <input wire:model="min_booking_hours" type="number" id="min_booking_hours" min="0" max="168"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('min_booking_hours') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Minimum hours required before appointment time</p>
                        @error('min_booking_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Time Slot Duration -->
                    <div>
                        <label for="booking_time_slots" class="block text-sm font-medium text-gray-700 mb-2">
                            Time Slot Intervals *
                        </label>
                        <select wire:model="booking_time_slots" id="booking_time_slots"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('booking_time_slots') border-red-300 @enderror">
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Interval between available time slots</p>
                        @error('booking_time_slots') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Cancellation Deadline -->
                    <div>
                        <label for="cancellation_deadline_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            Cancellation Deadline (Hours) *
                        </label>
                        <input wire:model="cancellation_deadline_hours" type="number" id="cancellation_deadline_hours" min="0" max="168"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('cancellation_deadline_hours') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Hours before appointment when cancellation is allowed</p>
                        @error('cancellation_deadline_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Booking Options -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center">
                        <input wire:model="allow_same_day_booking" type="checkbox" id="allow_same_day_booking"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="allow_same_day_booking" class="ml-2 block text-sm text-gray-900">
                            Allow same-day booking
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input wire:model="enable_waitlist" type="checkbox" id="enable_waitlist"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="enable_waitlist" class="ml-2 block text-sm text-gray-900">
                            Enable waitlist for fully booked slots
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input wire:model="require_payment_upfront" type="checkbox" id="require_payment_upfront"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="require_payment_upfront" class="ml-2 block text-sm text-gray-900">
                            Require payment upfront for bookings
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                    <span wire:loading.remove>Save Settings</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>