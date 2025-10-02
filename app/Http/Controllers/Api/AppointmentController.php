<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Get all appointments with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['client.user', 'staff.user', 'service', 'location']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $perPage = $request->get('per_page', 15);
        $appointments = $query->orderBy('appointment_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get specific appointment
     */
    public function show($id)
    {
        $appointment = Appointment::with(['client.user', 'staff.user', 'service', 'location', 'invoice'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    /**
     * Create new appointment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:now',
            'duration' => 'required|integer|min:15',
            'notes' => 'nullable|string|max:1000',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for conflicts
        $conflict = Appointment::where('staff_id', $request->staff_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member has a conflicting appointment at this time'
            ], 409);
        }

        $appointment = Appointment::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully',
            'data' => $appointment->load(['client.user', 'staff.user', 'service', 'location'])
        ], 201);
    }

    /**
     * Update appointment
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|exists:clients,id',
            'staff_id' => 'sometimes|exists:staff,id',
            'service_id' => 'sometimes|exists:services,id',
            'appointment_date' => 'sometimes|date',
            'duration' => 'sometimes|integer|min:15',
            'status' => 'sometimes|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:1000',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for conflicts if staff or date is being changed
        if ($request->has('staff_id') || $request->has('appointment_date')) {
            $staffId = $request->get('staff_id', $appointment->staff_id);
            $appointmentDate = $request->get('appointment_date', $appointment->appointment_date);

            $conflict = Appointment::where('staff_id', $staffId)
                ->where('appointment_date', $appointmentDate)
                ->where('id', '!=', $id)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member has a conflicting appointment at this time'
                ], 409);
            }
        }

        $appointment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment->load(['client.user', 'staff.user', 'service', 'location'])
        ]);
    }

    /**
     * Delete appointment
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ]);
    }

    /**
     * Get available time slots for a staff member on a specific date
     */
    public function availableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $staff = Staff::findOrFail($request->staff_id);
        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->date);

        // Get staff working hours for the day
        $workingHours = $staff->getWorkingHours($date->format('l'));
        
        if (!$workingHours) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member is not working on this day'
            ], 400);
        }

        // Get existing appointments for the day
        $existingAppointments = Appointment::where('staff_id', $request->staff_id)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        // Generate available time slots
        $slots = [];
        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHours['start']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHours['end']);
        $slotDuration = $service->duration ?? 60; // Default 60 minutes

        while ($startTime->addMinutes($slotDuration)->lte($endTime)) {
            $slotStart = $startTime->copy()->subMinutes($slotDuration);
            $slotEnd = $startTime->copy();

            // Check if this slot conflicts with existing appointments
            $hasConflict = $existingAppointments->some(function ($appointment) use ($slotStart, $slotEnd) {
                $apptStart = Carbon::parse($appointment->appointment_date);
                $apptEnd = $apptStart->copy()->addMinutes($appointment->duration);
                
                return $slotStart->lt($apptEnd) && $slotEnd->gt($apptStart);
            });

            if (!$hasConflict) {
                $slots[] = [
                    'start_time' => $slotStart->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'datetime' => $slotStart->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'staff_id' => $request->staff_id,
                'service_id' => $request->service_id,
                'available_slots' => $slots
            ]
        ]);
    }

    /**
     * Get appointment statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        $stats = [
            'total_appointments' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])->count(),
            'completed_appointments' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])
                ->where('status', 'cancelled')->count(),
            'no_show_appointments' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])
                ->where('status', 'no_show')->count(),
            'revenue' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')
                ->with('service')
                ->get()
                ->sum(function ($appointment) {
                    return $appointment->service->price ?? 0;
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}