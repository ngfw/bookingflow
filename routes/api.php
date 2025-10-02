<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Public service and location information
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::get('/services/categories', [ServiceController::class, 'categories']);
    Route::get('/services/category/{categoryId}', [ServiceController::class, 'byCategory']);
    Route::get('/services/popular', [ServiceController::class, 'popular']);
    
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{id}', [LocationController::class, 'show']);
    Route::get('/locations/{id}/services', [LocationController::class, 'services']);
    Route::get('/locations/{id}/staff', [LocationController::class, 'staff']);
    Route::get('/locations/{id}/business-hours', [LocationController::class, 'businessHours']);
    Route::get('/locations/{id}/amenities', [LocationController::class, 'amenities']);
    Route::get('/locations/{id}/contact', [LocationController::class, 'contact']);
    Route::get('/locations/nearby', [LocationController::class, 'nearby']);
});

// Protected API routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    
    // Appointments
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('/appointments/{id}/available-slots', [AppointmentController::class, 'availableSlots']);
    Route::get('/appointments/statistics', [AppointmentController::class, 'statistics']);
    
    // Clients
    Route::apiResource('clients', ClientController::class);
    Route::get('/clients/{id}/appointments', [ClientController::class, 'appointments']);
    Route::get('/clients/{id}/invoices', [ClientController::class, 'invoices']);
    Route::get('/clients/{id}/payments', [ClientController::class, 'payments']);
    Route::get('/clients/{id}/statistics', [ClientController::class, 'statistics']);
    
    // Services (admin/staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{id}', [ServiceController::class, 'update']);
        Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
        Route::get('/services/{id}/statistics', [ServiceController::class, 'statistics']);
    });
    
    // Locations (admin/staff only)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/locations/{id}/statistics', [LocationController::class, 'statistics']);
    });
    
    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{id}/refund', [PaymentController::class, 'refund']);
    Route::get('/payments/statistics', [PaymentController::class, 'statistics']);
    Route::get('/payments/methods', [PaymentController::class, 'methods']);
    
    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Additional admin-specific API endpoints can be added here
        Route::get('/admin/dashboard', function () {
            return response()->json([
                'success' => true,
                'message' => 'Admin dashboard data',
                'data' => [
                    'total_clients' => \App\Models\Client::count(),
                    'total_appointments' => \App\Models\Appointment::count(),
                    'total_revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount'),
                    'active_locations' => \App\Models\Location::where('is_active', true)->count(),
                ]
            ]);
        });
    });
});

// API Documentation endpoint
Route::get('/v1/documentation', function () {
    return response()->json([
        'success' => true,
        'message' => 'Beauty Salon Management API v1.0',
        'data' => [
            'version' => '1.0.0',
            'base_url' => url('/api/v1'),
            'authentication' => 'Bearer Token (Sanctum)',
            'endpoints' => [
                'auth' => [
                    'POST /register' => 'Register new user',
                    'POST /login' => 'Login user',
                    'POST /logout' => 'Logout user (authenticated)',
                    'GET /profile' => 'Get user profile (authenticated)',
                    'POST /refresh-token' => 'Refresh API token (authenticated)',
                ],
                'appointments' => [
                    'GET /appointments' => 'Get all appointments (authenticated)',
                    'POST /appointments' => 'Create appointment (authenticated)',
                    'GET /appointments/{id}' => 'Get specific appointment (authenticated)',
                    'PUT /appointments/{id}' => 'Update appointment (authenticated)',
                    'DELETE /appointments/{id}' => 'Delete appointment (authenticated)',
                    'GET /appointments/{id}/available-slots' => 'Get available time slots (authenticated)',
                    'GET /appointments/statistics' => 'Get appointment statistics (authenticated)',
                ],
                'clients' => [
                    'GET /clients' => 'Get all clients (authenticated)',
                    'POST /clients' => 'Create client (authenticated)',
                    'GET /clients/{id}' => 'Get specific client (authenticated)',
                    'PUT /clients/{id}' => 'Update client (authenticated)',
                    'DELETE /clients/{id}' => 'Delete client (authenticated)',
                    'GET /clients/{id}/appointments' => 'Get client appointments (authenticated)',
                    'GET /clients/{id}/invoices' => 'Get client invoices (authenticated)',
                    'GET /clients/{id}/payments' => 'Get client payments (authenticated)',
                    'GET /clients/{id}/statistics' => 'Get client statistics (authenticated)',
                ],
                'services' => [
                    'GET /services' => 'Get all services (public)',
                    'POST /services' => 'Create service (admin/staff)',
                    'GET /services/{id}' => 'Get specific service (public)',
                    'PUT /services/{id}' => 'Update service (admin/staff)',
                    'DELETE /services/{id}' => 'Delete service (admin/staff)',
                    'GET /services/categories' => 'Get service categories (public)',
                    'GET /services/category/{categoryId}' => 'Get services by category (public)',
                    'GET /services/popular' => 'Get popular services (public)',
                    'GET /services/{id}/statistics' => 'Get service statistics (admin/staff)',
                ],
                'locations' => [
                    'GET /locations' => 'Get all locations (public)',
                    'GET /locations/{id}' => 'Get specific location (public)',
                    'GET /locations/{id}/services' => 'Get location services (public)',
                    'GET /locations/{id}/staff' => 'Get location staff (public)',
                    'GET /locations/{id}/business-hours' => 'Get business hours (public)',
                    'GET /locations/{id}/amenities' => 'Get location amenities (public)',
                    'GET /locations/{id}/contact' => 'Get contact information (public)',
                    'GET /locations/nearby' => 'Find nearby locations (public)',
                    'GET /locations/{id}/statistics' => 'Get location statistics (admin/staff)',
                ],
                'payments' => [
                    'GET /payments' => 'Get all payments (authenticated)',
                    'POST /payments' => 'Process payment (authenticated)',
                    'GET /payments/{id}' => 'Get specific payment (authenticated)',
                    'PUT /payments/{id}' => 'Update payment (authenticated)',
                    'DELETE /payments/{id}' => 'Delete payment (authenticated)',
                    'POST /payments/{id}/refund' => 'Process refund (authenticated)',
                    'GET /payments/statistics' => 'Get payment statistics (authenticated)',
                    'GET /payments/methods' => 'Get payment methods (authenticated)',
                ],
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'token_lifetime' => 'Configurable via Sanctum',
            ],
            'rate_limiting' => [
                'default' => '60 requests per minute',
                'authenticated' => '1000 requests per minute',
            ],
            'response_format' => [
                'success' => 'boolean',
                'message' => 'string',
                'data' => 'object|array',
                'errors' => 'object (on validation errors)',
            ],
        ]
    ]);
});

