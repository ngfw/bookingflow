<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Get all services with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Service::with(['category', 'location']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $perPage = $request->get('per_page', 15);
        $services = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get specific service
     */
    public function show($id)
    {
        $service = Service::with(['category', 'location', 'appointments'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Create new service
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean',
            'requires_consultation' => 'boolean',
            'max_advance_booking_days' => 'nullable|integer|min:1',
            'cancellation_hours' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service->load(['category', 'location'])
        ], 201);
    }

    /**
     * Update service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric|min:0',
            'duration' => 'sometimes|integer|min:15',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'sometimes|boolean',
            'requires_consultation' => 'sometimes|boolean',
            'max_advance_booking_days' => 'nullable|integer|min:1',
            'cancellation_hours' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service->load(['category', 'location'])
        ]);
    }

    /**
     * Delete service
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        
        // Check if service has appointments
        if ($service->appointments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete service with existing appointments'
            ], 409);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Get service categories
     */
    public function categories()
    {
        $categories = Category::with('services')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get services by category
     */
    public function byCategory($categoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['category', 'location'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get popular services
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 10);
        $dateFrom = $request->get('date_from', now()->subMonths(3));
        $dateTo = $request->get('date_to', now());

        $popularServices = Service::with(['category', 'location'])
            ->whereHas('appointments', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_date', [$dateFrom, $dateTo])
                      ->where('status', 'completed');
            })
            ->withCount(['appointments' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('appointment_date', [$dateFrom, $dateTo])
                      ->where('status', 'completed');
            }])
            ->orderBy('appointments_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popularServices
        ]);
    }

    /**
     * Get service statistics
     */
    public function statistics($id)
    {
        $service = Service::findOrFail($id);

        $stats = [
            'total_bookings' => $service->appointments()->count(),
            'completed_bookings' => $service->appointments()->where('status', 'completed')->count(),
            'cancelled_bookings' => $service->appointments()->where('status', 'cancelled')->count(),
            'total_revenue' => $service->appointments()
                ->where('status', 'completed')
                ->get()
                ->sum(function ($appointment) use ($service) {
                    return $service->price;
                }),
            'average_rating' => $service->appointments()
                ->whereNotNull('rating')
                ->avg('rating'),
            'monthly_bookings' => $service->appointments()
                ->where('appointment_date', '>=', now()->startOfMonth())
                ->where('appointment_date', '<=', now()->endOfMonth())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}