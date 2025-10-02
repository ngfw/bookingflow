<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class MobileLocationServices extends Component
{
    public $userLocation = null;
    public $salonLocation = null;
    public $distance = null;
    public $estimatedTravelTime = null;
    public $locationPermission = false;
    public $showLocationModal = false;
    public $nearbyAppointments = [];
    public $locationHistory = [];
    public $currentView = 'location'; // location, nearby, history
    
    // Salon location (can be configured)
    public $salonLatitude = 37.7749; // Default to San Francisco coordinates
    public $salonLongitude = -122.4194;
    public $salonAddress = "123 Beauty Street, San Francisco, CA 94102";
    
    // Mobile-specific properties
    public $locationAccuracy = null;
    public $lastLocationUpdate = null;
    public $locationError = null;
    public $isTracking = false;
    public $trackingInterval = 300; // 5 minutes in seconds

    public function mount()
    {
        $this->salonLocation = [
            'latitude' => $this->salonLatitude,
            'longitude' => $this->salonLongitude,
            'address' => $this->salonAddress,
        ];
    }

    public function requestLocationPermission()
    {
        $this->dispatch('request-location-permission');
        $this->showLocationModal = true;
    }

    public function getCurrentLocation()
    {
        $this->dispatch('get-current-location');
        $this->isTracking = true;
    }

    public function updateLocation($latitude, $longitude, $accuracy = null)
    {
        $this->userLocation = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'timestamp' => now(),
        ];
        
        $this->locationAccuracy = $accuracy;
        $this->lastLocationUpdate = now();
        $this->locationError = null;
        $this->isTracking = false;
        
        // Calculate distance and travel time
        $this->calculateDistance();
        $this->calculateTravelTime();
        
        // Find nearby appointments
        $this->findNearbyAppointments();
        
        // Add to location history
        $this->addToLocationHistory();
    }

    public function locationError($error)
    {
        $this->locationError = $error;
        $this->isTracking = false;
        $this->locationPermission = false;
    }

    public function locationPermissionGranted()
    {
        $this->locationPermission = true;
        $this->showLocationModal = false;
        $this->getCurrentLocation();
    }

    public function locationPermissionDenied()
    {
        $this->locationPermission = false;
        $this->showLocationModal = false;
        $this->locationError = 'Location permission denied. Please enable location services to use this feature.';
    }

    private function calculateDistance()
    {
        if (!$this->userLocation || !$this->salonLocation) {
            return;
        }

        $earthRadius = 6371; // Earth's radius in kilometers
        
        $lat1 = deg2rad($this->userLocation['latitude']);
        $lon1 = deg2rad($this->userLocation['longitude']);
        $lat2 = deg2rad($this->salonLocation['latitude']);
        $lon2 = deg2rad($this->salonLocation['longitude']);
        
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $this->distance = round($earthRadius * $c, 2);
    }

    private function calculateTravelTime()
    {
        if (!$this->distance) {
            return;
        }

        // Estimate travel time based on distance and average speed
        $averageSpeed = 30; // km/h for city driving
        $this->estimatedTravelTime = round(($this->distance / $averageSpeed) * 60); // in minutes
    }

    private function findNearbyAppointments()
    {
        if (!$this->userLocation) {
            return;
        }

        // Find appointments within a certain radius (e.g., 5km)
        $radius = 5; // km
        $userLat = $this->userLocation['latitude'];
        $userLon = $this->userLocation['longitude'];
        
        // This would typically involve a more complex query with geospatial functions
        // For now, we'll simulate finding nearby appointments
        $appointments = Appointment::with(['client.user', 'service', 'staff.user'])
            ->where('appointment_date', '>=', Carbon::today())
            ->where('status', 'confirmed')
            ->get();

        $this->nearbyAppointments = $appointments->filter(function ($appointment) use ($userLat, $userLon, $radius) {
            // In a real implementation, this would use actual client location data
            // For now, we'll simulate based on appointment time proximity
            $appointmentTime = Carbon::parse($appointment->appointment_date);
            $timeDifference = abs($appointmentTime->diffInMinutes(now()));
            
            return $timeDifference <= 60; // Within 1 hour
        })->take(5)->values();
    }

    private function addToLocationHistory()
    {
        if (!$this->userLocation) {
            return;
        }

        $this->locationHistory[] = [
            'latitude' => $this->userLocation['latitude'],
            'longitude' => $this->userLocation['longitude'],
            'timestamp' => $this->userLocation['timestamp'],
            'distance' => $this->distance,
        ];

        // Keep only last 10 location updates
        if (count($this->locationHistory) > 10) {
            $this->locationHistory = array_slice($this->locationHistory, -10);
        }
    }

    public function startLocationTracking()
    {
        $this->isTracking = true;
        $this->dispatch('start-location-tracking', ['interval' => $this->trackingInterval]);
    }

    public function stopLocationTracking()
    {
        $this->isTracking = false;
        $this->dispatch('stop-location-tracking');
    }

    public function setView($view)
    {
        $this->currentView = $view;
    }

    public function getDirections()
    {
        if (!$this->userLocation || !$this->salonLocation) {
            return;
        }

        $this->dispatch('open-directions', [
            'destination' => $this->salonLocation,
            'origin' => $this->userLocation,
        ]);
    }

    public function shareLocation()
    {
        if (!$this->userLocation) {
            return;
        }

        $this->dispatch('share-location', [
            'location' => $this->userLocation,
            'message' => "I'm at: " . $this->userLocation['latitude'] . ", " . $this->userLocation['longitude'],
        ]);
    }

    public function render()
    {
        return view('livewire.public.mobile-location-services');
    }
}

