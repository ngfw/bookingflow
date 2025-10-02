<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Get all locations with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Location::with(['franchise']);

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('franchise_id')) {
            $query->where('franchise_id', $request->franchise_id);
        }

        if ($request->has('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        if ($request->has('state')) {
            $query->where('state', 'like', "%{$request->state}%");
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $locations = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get specific location
     */
    public function show($id)
    {
        $location = Location::with(['franchise', 'staff.user', 'services'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }

    /**
     * Get location services
     */
    public function services($id)
    {
        $location = Location::findOrFail($id);
        $services = $location->services()->with('category')->where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get location staff
     */
    public function staff($id)
    {
        $location = Location::findOrFail($id);
        $staff = $location->staff()->with('user')->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->get();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    /**
     * Get location business hours
     */
    public function businessHours($id)
    {
        $location = Location::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'business_hours' => $location->business_hours,
                'formatted_hours' => $location->business_hours_formatted,
                'is_open' => $location->isOpen(),
                'next_open_time' => $location->getNextOpenTime(),
                'timezone' => $location->timezone,
            ]
        ]);
    }

    /**
     * Get location amenities
     */
    public function amenities($id)
    {
        $location = Location::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'amenities' => $location->amenities,
                'amenities_list' => $location->amenities_list,
            ]
        ]);
    }

    /**
     * Get location statistics
     */
    public function statistics($id)
    {
        $location = Location::findOrFail($id);

        $stats = [
            'total_staff' => $location->getStaffCount(),
            'active_staff' => $location->getActiveStaffCount(),
            'today_appointments' => $location->getTodayAppointmentsCount(),
            'today_revenue' => $location->getTodayRevenue(),
            'total_services' => $location->services()->count(),
            'active_services' => $location->services()->where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Find locations near coordinates
     */
    public function nearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100', // in kilometers
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->get('radius', 10); // Default 10km radius

        $locations = Location::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($location) use ($latitude, $longitude) {
                $distance = $location->getDistanceFrom($latitude, $longitude);
                return [
                    'location' => $location,
                    'distance' => $distance,
                ];
            })
            ->filter(function ($item) use ($radius) {
                return $item['distance'] !== null && $item['distance'] <= $radius;
            })
            ->sortBy('distance')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get location contact information
     */
    public function contact($id)
    {
        $location = Location::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $location->name,
                'address' => $location->full_address,
                'phone' => $location->phone,
                'email' => $location->email,
                'website' => $location->website,
                'coordinates' => [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ],
            ]
        ]);
    }
}