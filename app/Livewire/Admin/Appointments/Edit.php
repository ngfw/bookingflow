<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;

class Edit extends Component
{
    public Appointment $appointment;

    // Form fields
    public $client_id = '';
    public $service_id = '';
    public $staff_id = '';
    public $appointment_date = '';
    public $appointment_time = '';
    public $notes = '';
    public $status = 'pending';

    // Time slot management
    public $availableTimeSlots = [];
    public $originalDateTime = '';

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'service_id' => 'required|exists:services,id',
        'staff_id' => 'nullable|exists:staff,id',
        'appointment_date' => 'required|date',
        'appointment_time' => 'required',
        'notes' => 'nullable|string|max:1000',
        'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
    ];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
        
        // Load existing appointment data
        $this->client_id = $appointment->client_id;
        $this->service_id = $appointment->service_id;
        $this->staff_id = $appointment->staff_id;
        $this->appointment_date = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        $this->appointment_time = Carbon::parse($appointment->appointment_date)->format('H:i');
        $this->notes = $appointment->notes;
        $this->status = $appointment->status;
        
        $this->originalDateTime = $appointment->appointment_date;
        
        // Load available time slots
        $this->loadAvailableTimeSlots();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        if (in_array($propertyName, ['service_id', 'appointment_date', 'staff_id'])) {
            $this->loadAvailableTimeSlots();
        }
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->service_id || !$this->appointment_date) {
            $this->availableTimeSlots = [];
            return;
        }

        $service = Service::find($this->service_id);
        $appointmentDate = Carbon::parse($this->appointment_date);
        
        // Get staff members who can perform this service
        $availableStaff = Staff::whereHas('services', function($query) {
            $query->where('service_id', $this->service_id);
        })->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        if ($availableStaff->isEmpty()) {
            $this->availableTimeSlots = [];
            return;
        }

        // If no specific staff selected, use first available staff
        if (!$this->staff_id) {
            $this->staff_id = $availableStaff->first()->id;
        }

        $staff = Staff::find($this->staff_id);
        if (!$staff) {
            $this->availableTimeSlots = [];
            return;
        }

        // Get staff schedule for the selected date
        $schedule = Schedule::where('staff_id', $staff->id)
            ->where('day_of_week', $appointmentDate->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            $this->availableTimeSlots = [];
            return;
        }

        // Generate time slots
        $this->generateTimeSlots($schedule, $service, $appointmentDate);
    }

    public function generateTimeSlots($schedule, $service, $appointmentDate)
    {
        $timeSlots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $serviceDuration = $service->duration_minutes;
        $bufferTime = $service->buffer_time_minutes ?? 0;
        $slotDuration = $serviceDuration + $bufferTime;

        // Get existing appointments for this staff on this date (excluding current appointment)
        $existingAppointments = Appointment::where('staff_id', $schedule->staff_id)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('id', '!=', $this->appointment->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        $currentTime = $startTime->copy();
        
        while ($currentTime->addMinutes($slotDuration)->lte($endTime)) {
            $slotStart = $currentTime->copy()->subMinutes($slotDuration);
            $slotEnd = $currentTime->copy();
            
            // Check if this time slot conflicts with existing appointments
            $hasConflict = $existingAppointments->some(function($appointment) use ($slotStart, $slotEnd, $serviceDuration) {
                $appointmentStart = Carbon::parse($appointment->appointment_date);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->service->duration_minutes);
                
                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            if (!$hasConflict) {
                $timeSlots[] = [
                    'time' => $slotStart->format('H:i'),
                    'display' => $slotStart->format('g:i A'),
                    'end_time' => $slotEnd->format('g:i A'),
                ];
            }
        }

        $this->availableTimeSlots = $timeSlots;
    }

    public function save()
    {
        $this->validate();

        // Combine date and time
        $appointmentDateTime = Carbon::parse($this->appointment_date . ' ' . $this->appointment_time);

        // Check if the appointment time has changed
        $hasTimeChanged = $appointmentDateTime->ne(Carbon::parse($this->originalDateTime));

        if ($hasTimeChanged) {
            // Check for conflicts one more time
            $conflict = Appointment::where('staff_id', $this->staff_id)
                ->where('appointment_date', $appointmentDateTime)
                ->where('id', '!=', $this->appointment->id)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($conflict) {
                session()->flash('error', 'This time slot is no longer available. Please select another time.');
                $this->loadAvailableTimeSlots();
                return;
            }
        }

        $this->appointment->update([
            'client_id' => $this->client_id,
            'service_id' => $this->service_id,
            'staff_id' => $this->staff_id,
            'appointment_date' => $appointmentDateTime,
            'notes' => $this->notes,
            'status' => $this->status,
            'total_price' => Service::find($this->service_id)->price,
        ]);

        session()->flash('success', 'Appointment updated successfully!');
        return redirect()->route('admin.appointments.index');
    }

    public function cancelAppointment()
    {
        $this->appointment->update(['status' => 'cancelled']);
        session()->flash('success', 'Appointment cancelled successfully!');
        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        $clients = Client::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        $services = Service::where('is_active', true)->orderBy('name')->get();

        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        return view('livewire.admin.appointments.edit', [
            'clients' => $clients,
            'services' => $services,
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
