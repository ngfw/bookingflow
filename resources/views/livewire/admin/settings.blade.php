<div class="p-6" x-data="{ activeTab: 'general' }">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Salon Settings</h1>
            <p class="text-gray-600">Configure your salon's comprehensive settings and preferences.</p>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    General Information
                </button>
                <button @click="activeTab = 'appearance'" :class="activeTab === 'appearance' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Appearance & Branding
                </button>
                <button @click="activeTab = 'booking'" :class="activeTab === 'booking' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Booking Settings
                </button>
                <button @click="activeTab = 'analytics'" :class="activeTab === 'analytics' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Analytics & SEO
                </button>
                <button @click="activeTab = 'localization'" :class="activeTab === 'localization' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Localization
                </button>
                <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Notifications
                </button>
                <button @click="activeTab = 'legal'" :class="activeTab === 'legal' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Legal & Privacy
                </button>
            </nav>
        </div>

        <form wire:submit.prevent="saveSettings" class="space-y-8">
            <!-- General Information Tab -->
            <div x-show="activeTab === 'general'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">General Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="salon_name" class="block text-sm font-medium text-gray-700 mb-2">Salon Name *</label>
                        <input wire:model="salon_name" type="text" id="salon_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('salon_name') border-red-300 @enderror">
                        @error('salon_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="salon_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input wire:model="salon_phone" type="text" id="salon_phone" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        @error('salon_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="salon_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input wire:model="salon_email" type="email" id="salon_email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        @error('salon_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="salon_website" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input wire:model="salon_website" type="url" id="salon_website" placeholder="https://..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        @error('salon_website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="salon_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model="salon_description" id="salon_description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                        @error('salon_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Address Information & Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="salon_address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                            <input wire:model="salon_address" type="text" id="salon_address" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('salon_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="salon_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input wire:model="salon_city" type="text" id="salon_city" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('salon_city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="salon_state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                            <input wire:model="salon_state" type="text" id="salon_state" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('salon_state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="salon_zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code</label>
                            <input wire:model="salon_zip" type="text" id="salon_zip" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('salon_zip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="salon_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <input wire:model="salon_country" type="text" id="salon_country" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('salon_country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Coordinates & Geocoding -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Coordinates & Mapping</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="salon_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                                <input wire:model="salon_latitude" type="number" step="0.000001" id="salon_latitude" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" 
                                       placeholder="40.712776">
                                @error('salon_latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="salon_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                                <input wire:model="salon_longitude" type="number" step="0.000001" id="salon_longitude" 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" 
                                       placeholder="-74.005974">
                                @error('salon_longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-end">
                                <button type="button" wire:click="geocodeAddress" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Get Coordinates
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Fill in the address above and click "Get Coordinates" to automatically populate latitude and longitude for map display.
                        </p>
                    </div>

                    <!-- Service Area Settings -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Service Area Restrictions</h4>
                        
                        <div class="flex items-center mb-4">
                            <input wire:model="enable_location_restriction" type="checkbox" id="enable_location_restriction" 
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="enable_location_restriction" class="ml-2 block text-sm text-gray-900">
                                Enable location-based booking restrictions
                            </label>
                        </div>

                        @if($enable_location_restriction)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="service_radius" class="block text-sm font-medium text-gray-700 mb-2">Service Radius</label>
                                    <input wire:model="service_radius" type="number" min="1" max="500" id="service_radius" 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                    @error('service_radius') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="service_radius_unit" class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                                    <select wire:model="service_radius_unit" id="service_radius_unit" 
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                        <option value="miles">Miles</option>
                                        <option value="kilometers">Kilometers</option>
                                    </select>
                                    @error('service_radius_unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="location_verification_message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Location Verification Message
                                </label>
                                <textarea wire:model="location_verification_message" id="location_verification_message" rows="3" 
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" 
                                          placeholder="Please confirm you are located within our service area before booking."></textarea>
                                <p class="mt-1 text-sm text-gray-500">This message will be shown to customers during booking to confirm their location.</p>
                                @error('location_verification_message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <!-- Map Display -->
                    @if($salon_latitude && $salon_longitude)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Location Preview</h4>
                            <div class="bg-gray-100 rounded-lg p-4">
                                <div id="salon-map" class="h-64 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-lg font-medium text-gray-600">Interactive Map</div>
                                        <div class="text-sm text-gray-500">Coordinates: {{ $salon_latitude }}, {{ $salon_longitude }}</div>
                                        <a href="https://www.google.com/maps?q={{ $salon_latitude }},{{ $salon_longitude }}" 
                                           target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                            View on Google Maps
                                        </a>
                                    </div>
                                </div>
                                @if($enable_location_restriction && $service_radius)
                                    <p class="mt-2 text-sm text-gray-600 text-center">
                                        Service area: {{ $service_radius }} {{ $service_radius_unit }} radius
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Social Media Section -->
                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Social Media Links</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                            <input wire:model="facebook_url" type="url" id="facebook_url" placeholder="https://facebook.com/..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('facebook_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                            <input wire:model="instagram_url" type="url" id="instagram_url" placeholder="https://instagram.com/..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('instagram_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                            <input wire:model="twitter_url" type="url" id="twitter_url" placeholder="https://twitter.com/..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('twitter_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                            <input wire:model="youtube_url" type="url" id="youtube_url" placeholder="https://youtube.com/..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('youtube_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance & Branding Tab -->
            <div x-show="activeTab === 'appearance'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Appearance & Branding</h2>
                
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

                <!-- Logo Upload Section -->
                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Logo Management</h3>
                    
                    <div class="space-y-4">
                        <!-- Current Logo Display -->
                        @if($current_logo_path)
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $current_logo_path) }}" 
                                         alt="Current Logo" 
                                         class="h-16 w-auto max-w-32 object-contain border border-gray-200 rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">Current logo</p>
                                    <p class="text-xs text-gray-500">{{ basename($current_logo_path) }}</p>
                                </div>
                                <button type="button" 
                                        wire:click="removeLogo"
                                        wire:confirm="Are you sure you want to remove the current logo?"
                                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove Logo
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">No logo uploaded</p>
                            </div>
                        @endif

                        <!-- Logo Upload Form -->
                        <div class="space-y-4">
                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $current_logo_path ? 'Replace Logo' : 'Upload Logo' }}
                                </label>
                                <input wire:model="logo" 
                                       type="file" 
                                       id="logo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                                @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if($logo)
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm text-gray-600">
                                        Selected: {{ $logo->getClientOriginalName() }}
                                        ({{ number_format($logo->getSize() / 1024, 1) }} KB)
                                    </div>
                                    <button type="button" 
                                            wire:click="uploadLogo"
                                            wire:loading.attr="disabled"
                                            wire:target="uploadLogo"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 disabled:opacity-50">
                                        <span wire:loading.remove wire:target="uploadLogo">Upload Logo</span>
                                        <span wire:loading wire:target="uploadLogo" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Uploading...
                                        </span>
                                    </button>
                                </div>
                            @endif

                            <div class="text-xs text-gray-500">
                                <p>• Supported formats: JPG, PNG, GIF, SVG</p>
                                <p>• Maximum file size: 2MB</p>
                                <p>• Recommended dimensions: 300x100px (3:1 ratio)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Business Hours</h3>
                    <div class="space-y-4">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="flex items-center space-x-4">
                                <div class="w-24">
                                    <input wire:model="business_hours.{{ $day }}.enabled" type="checkbox" id="{{ $day }}_enabled"
                                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                    <label for="{{ $day }}_enabled" class="ml-2 text-sm font-medium text-gray-700 capitalize">
                                        {{ $day }}
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input wire:model="business_hours.{{ $day }}.open" type="time" 
                                           class="border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           @if(!$business_hours[$day]['enabled']) disabled @endif>
                                    <span class="text-gray-500">to</span>
                                    <input wire:model="business_hours.{{ $day }}.close" type="time" 
                                           class="border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"
                                           @if(!$business_hours[$day]['enabled']) disabled @endif>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Booking Settings Tab -->
            <div x-show="activeTab === 'booking'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Booking Settings</h2>
                
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

            <!-- Analytics & SEO Tab -->
            <div x-show="activeTab === 'analytics'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Analytics & SEO</h2>
                
                <!-- Google Analytics -->
                <div class="mb-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Google Analytics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-2">Analytics Tracking ID</label>
                            <input wire:model="google_analytics_id" type="text" id="google_analytics_id" placeholder="G-XXXXXXXXXX" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @error('google_analytics_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center">
                            <input wire:model="google_analytics_enabled" type="checkbox" id="google_analytics_enabled"
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="google_analytics_enabled" class="ml-2 block text-sm text-gray-900">
                                Enable Google Analytics tracking
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Google reCAPTCHA -->
                <div class="mb-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Google reCAPTCHA</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">reCAPTCHA Setup Instructions:</p>
                                <ol class="mt-2 list-decimal list-inside space-y-1">
                                    <li>Visit <a href="https://www.google.com/recaptcha/admin" target="_blank" class="underline">Google reCAPTCHA Console</a></li>
                                    <li>Create a new site and choose "reCAPTCHA v2" → "I'm not a robot"</li>
                                    <li>Add your domain to the list</li>
                                    <li>Copy the Site Key and Secret Key below</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center mb-4">
                            <input wire:model="recaptcha_enabled" type="checkbox" id="recaptcha_enabled"
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="recaptcha_enabled" class="ml-2 block text-sm text-gray-900 font-medium">
                                Enable reCAPTCHA for registration page
                            </label>
                        </div>

                        @if($recaptcha_enabled)
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700 mb-2">Site Key (Public)</label>
                                    <input wire:model="recaptcha_site_key" type="text" id="recaptcha_site_key" 
                                           placeholder="6Lc..." 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                    <p class="mt-1 text-sm text-gray-500">This key is used in the HTML code your site serves to users</p>
                                    @error('recaptcha_site_key') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700 mb-2">Secret Key (Private)</label>
                                    <input wire:model="recaptcha_secret_key" type="password" id="recaptcha_secret_key" 
                                           placeholder="6Lc..." 
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                    <p class="mt-1 text-sm text-gray-500">This key is used for communication between your site and reCAPTCHA. Keep this secret!</p>
                                    @error('recaptcha_secret_key') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            @if($recaptcha_site_key && $recaptcha_secret_key)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-green-800">reCAPTCHA is configured and ready!</span>
                                    </div>
                                    <p class="mt-2 text-sm text-green-700">reCAPTCHA will now be displayed on the registration page to prevent spam registrations.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- SEO Settings -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">SEO Settings</h3>
                    <div class="space-y-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input wire:model="meta_title" type="text" id="meta_title" maxlength="60" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                            @error('meta_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea wire:model="meta_description" id="meta_description" rows="3" maxlength="160" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                            <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                            @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                            <input wire:model="meta_keywords" type="text" id="meta_keywords" 
                                   placeholder="salon, beauty, hair, nails, spa" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                            @error('meta_keywords') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localization Tab -->
            <div x-show="activeTab === 'localization'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Localization Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Language Settings -->
                    <div>
                        <label for="default_language" class="block text-sm font-medium text-gray-700 mb-2">Default Language *</label>
                        <select wire:model="default_language" id="default_language" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @foreach($available_languages as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('default_language') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center">
                        <input wire:model="enable_multi_language" type="checkbox" id="enable_multi_language"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="enable_multi_language" class="ml-2 block text-sm text-gray-900">
                            Enable multi-language support
                        </label>
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone *</label>
                        <select wire:model="timezone" id="timezone" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @foreach($timezones as $tz => $label)
                                <option value="{{ $tz }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Date Format -->
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format *</label>
                        <select wire:model="date_format" id="date_format" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <option value="M d, Y">Jan 15, 2024</option>
                            <option value="d/m/Y">15/01/2024</option>
                            <option value="m/d/Y">01/15/2024</option>
                            <option value="Y-m-d">2024-01-15</option>
                            <option value="d-m-Y">15-01-2024</option>
                        </select>
                        @error('date_format') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Time Format -->
                    <div>
                        <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">Time Format *</label>
                        <select wire:model="time_format" id="time_format" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <option value="g:i A">12:30 PM</option>
                            <option value="H:i">12:30</option>
                        </select>
                        @error('time_format') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                        <select wire:model="currency" id="currency" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            @foreach($currencies as $code => $details)
                                <option value="{{ $code }}">{{ $details['name'] }} ({{ $details['symbol'] }})</option>
                            @endforeach
                        </select>
                        @error('currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div x-show="activeTab === 'notifications'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Notification Settings</h2>
                
                <div class="space-y-6">
                    <!-- Email Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="email_notifications_enabled" class="text-sm font-medium text-gray-900">Email Notifications</label>
                            <p class="text-sm text-gray-500">Send email notifications for appointments and updates</p>
                        </div>
                        <input wire:model="email_notifications_enabled" type="checkbox" id="email_notifications_enabled"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    </div>

                    <!-- SMS Notifications -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="sms_notifications_enabled" class="text-sm font-medium text-gray-900">SMS Notifications</label>
                            <p class="text-sm text-gray-500">Send SMS notifications for appointments and reminders</p>
                        </div>
                        <input wire:model="sms_notifications_enabled" type="checkbox" id="sms_notifications_enabled"
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    </div>

                    <!-- Notification Email -->
                    <div>
                        <label for="notification_email" class="block text-sm font-medium text-gray-700 mb-2">Notification Email Address</label>
                        <input wire:model="notification_email" type="email" id="notification_email" 
                               placeholder="notifications@yoursalon.com" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        <p class="mt-1 text-sm text-gray-500">Email address to receive system notifications</p>
                        @error('notification_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Legal & Privacy Tab -->
            <div x-show="activeTab === 'legal'" class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Legal & Privacy</h2>
                
                <div class="space-y-6">
                    <!-- Terms of Service -->
                    <div>
                        <label for="terms_of_service" class="block text-sm font-medium text-gray-700 mb-2">Terms of Service</label>
                        <textarea wire:model="terms_of_service" id="terms_of_service" rows="8" 
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter your salon's terms of service</p>
                        @error('terms_of_service') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Privacy Policy -->
                    <div>
                        <label for="privacy_policy" class="block text-sm font-medium text-gray-700 mb-2">Privacy Policy</label>
                        <textarea wire:model="privacy_policy" id="privacy_policy" rows="8" 
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter your salon's privacy policy</p>
                        @error('privacy_policy') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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