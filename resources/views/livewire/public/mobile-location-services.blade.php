<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <div class="max-w-md mx-auto px-4 py-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Location Services</h1>
            <p class="text-md text-gray-600">Find us and get directions</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-xl shadow-sm p-1 mb-6">
            <div class="flex">
                <button wire:click="setView('location')" 
                        class="flex-1 py-3 px-4 text-sm font-medium rounded-lg {{ $currentView === 'location' ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Location
                </button>
                <button wire:click="setView('nearby')" 
                        class="flex-1 py-3 px-4 text-sm font-medium rounded-lg {{ $currentView === 'nearby' ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Nearby
                </button>
                <button wire:click="setView('history')" 
                        class="flex-1 py-3 px-4 text-sm font-medium rounded-lg {{ $currentView === 'history' ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    History
                </button>
            </div>
        </div>

        @if($currentView === 'location')
            <!-- Location View -->
            <div class="space-y-6">
                <!-- Salon Information -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Our Salon</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Beauty Salon</h3>
                                <p class="text-sm text-gray-600">{{ $salonAddress }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Business Hours</h3>
                                <p class="text-sm text-gray-600">Mon-Fri: 9AM-7PM<br>Sat-Sun: 10AM-6PM</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Contact</h3>
                                <p class="text-sm text-gray-600">(555) 123-4567<br>info@beautysalon.com</p>
                            </div>
                        </div>
                    </div>

                    <button wire:click="getDirections" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold mt-6">
                        Get Directions
                    </button>
                </div>

                <!-- Current Location -->
                @if($userLocation)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Location</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Distance:</span>
                                <span class="font-semibold">{{ $distance }} km</span>
                            </div>
                            
                            @if($estimatedTravelTime)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Travel Time:</span>
                                    <span class="font-semibold">{{ $estimatedTravelTime }} minutes</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Accuracy:</span>
                                <span class="font-semibold">±{{ $locationAccuracy }}m</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Last Updated:</span>
                                <span class="font-semibold text-sm">{{ $lastLocationUpdate->format('g:i A') }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-6">
                            <button wire:click="shareLocation" 
                                    class="bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium">
                                Share Location
                            </button>
                            <button wire:click="getCurrentLocation" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
                                Update Location
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Location Permission Request -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Enable Location Services</h2>
                        
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-4">Enable location services to get directions and find nearby appointments.</p>
                        </div>

                        <button wire:click="requestLocationPermission" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                            Enable Location Services
                        </button>
                    </div>
                @endif

                <!-- Location Tracking -->
                @if($userLocation)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Location Tracking</h2>
                        
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-600">Auto-update location:</span>
                            <span class="font-semibold {{ $isTracking ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $isTracking ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex space-x-3">
                            @if(!$isTracking)
                                <button wire:click="startLocationTracking" 
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium">
                                    Start Tracking
                                </button>
                            @else
                                <button wire:click="stopLocationTracking" 
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium">
                                    Stop Tracking
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        @elseif($currentView === 'nearby')
            <!-- Nearby Appointments -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Nearby Appointments</h2>
                
                @if(count($nearbyAppointments) > 0)
                    <div class="space-y-4">
                        @foreach($nearbyAppointments as $appointment)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $appointment->service->name }}</h3>
                                    <span class="text-sm text-gray-600">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>{{ $appointment->staff->user->name }}</span>
                                    <span>{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M j') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500">No nearby appointments found</p>
                    </div>
                @endif
            </div>

        @elseif($currentView === 'history')
            <!-- Location History -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Location History</h2>
                
                @if(count($locationHistory) > 0)
                    <div class="space-y-3">
                        @foreach($locationHistory as $location)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ number_format($location['latitude'], 6) }}, {{ number_format($location['longitude'], 6) }}</p>
                                    <p class="text-sm text-gray-600">{{ $location['timestamp']->format('M j, g:i A') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">{{ $location['distance'] }} km</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No location history available</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Error Messages -->
        @if($locationError)
            <div class="fixed bottom-4 left-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
                <div class="flex items-center justify-between">
                    <span>{{ $locationError }}</span>
                    <button wire:click="$set('locationError', null)" class="ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- JavaScript for Location Services -->
        <script>
            document.addEventListener('livewire:init', function () {
                // Request location permission
                Livewire.on('request-location-permission', function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                @this.call('updateLocation', 
                                    position.coords.latitude, 
                                    position.coords.longitude, 
                                    position.coords.accuracy
                                );
                                @this.call('locationPermissionGranted');
                            },
                            function(error) {
                                let errorMessage = 'Location access denied.';
                                switch(error.code) {
                                    case error.PERMISSION_DENIED:
                                        errorMessage = 'Location access denied by user.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        errorMessage = 'Location information is unavailable.';
                                        break;
                                    case error.TIMEOUT:
                                        errorMessage = 'Location request timed out.';
                                        break;
                                }
                                @this.call('locationError', errorMessage);
                                @this.call('locationPermissionDenied');
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 300000
                            }
                        );
                    } else {
                        @this.call('locationError', 'Geolocation is not supported by this browser.');
                    }
                });

                // Get current location
                Livewire.on('get-current-location', function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                @this.call('updateLocation', 
                                    position.coords.latitude, 
                                    position.coords.longitude, 
                                    position.coords.accuracy
                                );
                            },
                            function(error) {
                                @this.call('locationError', 'Failed to get current location.');
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    }
                });

                // Start location tracking
                Livewire.on('start-location-tracking', function (data) {
                    const interval = data.interval || 300000; // 5 minutes default
                    
                    // Get initial location
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            @this.call('updateLocation', 
                                position.coords.latitude, 
                                position.coords.longitude, 
                                position.coords.accuracy
                            );
                        }
                    );
                    
                    // Set up interval for tracking
                    window.locationTrackingInterval = setInterval(function() {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                @this.call('updateLocation', 
                                    position.coords.latitude, 
                                    position.coords.longitude, 
                                    position.coords.accuracy
                                );
                            }
                        );
                    }, interval);
                });

                // Stop location tracking
                Livewire.on('stop-location-tracking', function () {
                    if (window.locationTrackingInterval) {
                        clearInterval(window.locationTrackingInterval);
                        window.locationTrackingInterval = null;
                    }
                });

                // Open directions in maps app
                Livewire.on('open-directions', function (data) {
                    const destination = data.destination;
                    const origin = data.origin;
                    
                    // Try to open in native maps app first
                    const mapsUrl = `https://www.google.com/maps/dir/${origin.latitude},${origin.longitude}/${destination.latitude},${destination.longitude}`;
                    window.open(mapsUrl, '_blank');
                });

                // Share location
                Livewire.on('share-location', function (data) {
                    if (navigator.share) {
                        navigator.share({
                            title: 'My Location',
                            text: data.message,
                            url: `https://www.google.com/maps?q=${data.location.latitude},${data.location.longitude}`
                        });
                    } else {
                        // Fallback to copying to clipboard
                        navigator.clipboard.writeText(data.message).then(function() {
                            alert('Location copied to clipboard!');
                        });
                    }
                });
            });
        </script>
    </div>
</div>

