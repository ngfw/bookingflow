<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Get all clients with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Client::with(['user', 'appointments.service', 'invoices']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_active', $request->is_active);
            });
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $perPage = $request->get('per_page', 15);
        $clients = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    /**
     * Get specific client
     */
    public function show($id)
    {
        $client = Client::with([
            'user',
            'appointments.service',
            'appointments.staff.user',
            'invoices',
            'payments',
            'loyaltyPoints'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $client
        ]);
    }

    /**
     * Create new client
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'preferred_communication' => 'nullable|in:email,phone,sms',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'is_active' => true,
        ]);

        // Create client profile
        $client = Client::create([
            'user_id' => $user->id,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'medical_conditions' => $request->medical_conditions,
            'allergies' => $request->allergies,
            'preferred_communication' => $request->preferred_communication,
            'location_id' => $request->location_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'data' => $client->load('user')
        ], 201);
    }

    /**
     * Update client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $user = $client->user;

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'preferred_communication' => 'nullable|in:email,phone,sms',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user data
        $userData = $request->only([
            'name', 'email', 'phone', 'address', 'date_of_birth', 'gender', 'is_active'
        ]);
        $user->update($userData);

        // Update client data
        $clientData = $request->only([
            'emergency_contact_name', 'emergency_contact_phone', 'medical_conditions',
            'allergies', 'preferred_communication', 'location_id'
        ]);
        $client->update($clientData);

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => $client->load('user')
        ]);
    }

    /**
     * Delete client
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $user = $client->user;

        // Soft delete client and user
        $client->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    }

    /**
     * Get client appointments
     */
    public function appointments($id, Request $request)
    {
        $client = Client::findOrFail($id);
        
        $query = $client->appointments()->with(['service', 'staff.user', 'location']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 15);
        $appointments = $query->orderBy('appointment_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get client invoices
     */
    public function invoices($id, Request $request)
    {
        $client = Client::findOrFail($id);
        
        $query = $client->invoices()->with(['appointment.service']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Get client payments
     */
    public function payments($id, Request $request)
    {
        $client = Client::findOrFail($id);
        
        $query = $client->payments()->with(['invoice']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Get client statistics
     */
    public function statistics($id)
    {
        $client = Client::findOrFail($id);

        $stats = [
            'total_appointments' => $client->appointments()->count(),
            'completed_appointments' => $client->appointments()->where('status', 'completed')->count(),
            'total_spent' => $client->payments()->where('status', 'completed')->sum('amount'),
            'loyalty_points' => $client->loyaltyPoints()->sum('points'),
            'last_appointment' => $client->appointments()->latest('appointment_date')->first(),
            'favorite_service' => $client->appointments()
                ->with('service')
                ->get()
                ->groupBy('service_id')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}