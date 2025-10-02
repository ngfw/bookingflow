<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;

class Create extends Component
{
    // Form fields
    public $client_id = '';
    public $service_id = '';
    public $staff_id = '';
    public $appointment_date = '';
    public $appointment_time = '';
    public $notes = '';
    public $status = 'pending';
    
    // Recurring appointment fields
    public $is_recurring = false;
    public $recurring_pattern = '';
    public $recurring_end_date = '';

    // Time slot management
    public $availableTimeSlots = [];
    public $selectedDate = '';
    public $selectedStaff = '';
    public $quickTimeSelection = false;

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'service_id' => 'required|exists:services,id',
        'staff_id' => 'nullable|exists:staff,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'notes' => 'nullable|string|max:1000',
        'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
        'is_recurring' => 'boolean',
        'recurring_pattern' => 'required_if:is_recurring,true|in:weekly,biweekly,monthly',
        'recurring_end_date' => 'required_if:is_recurring,true|date|after:appointment_date',
    ];

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

        // If no staff assigned to service, get all active staff as fallback
        if ($availableStaff->isEmpty()) {
            $availableStaff = Staff::whereHas('user', function($query) {
                $query->where('is_active', true);
            })->get();
        }

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

        // Get staff schedule for the selected date or use default working hours
        $schedule = Schedule::where('staff_id', $staff->id)
            ->where('date', $appointmentDate->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->first();

        // If no specific schedule, create a default schedule based on staff working days
        if (!$schedule) {
            $schedule = $this->createDefaultSchedule($staff, $appointmentDate);
        }

        if (!$schedule) {
            $this->availableTimeSlots = [];
            return;
        }

        // Generate time slots
        $this->generateTimeSlots($schedule, $service, $appointmentDate);
    }

    private function createDefaultSchedule($staff, $appointmentDate)
    {
        // Check if staff works on this day of week
        $dayOfWeek = $appointmentDate->format('l'); // Monday, Tuesday, etc.
        
        if (!$staff->working_days || !in_array($dayOfWeek, $staff->working_days)) {
            return null;
        }

        // Create a temporary schedule object with default hours
        $defaultStartTime = $staff->default_start_time ?: '09:00';
        $defaultEndTime = $staff->default_end_time ?: '17:00';

        return (object) [
            'staff_id' => $staff->id,
            'start_time' => $defaultStartTime,
            'end_time' => $defaultEndTime,
            'break_start' => null,
            'break_end' => null,
        ];
    }

    public function generateTimeSlots($schedule, $service, $appointmentDate)
    {
        $timeSlots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $serviceDuration = $service->duration_minutes;
        $bufferTime = $service->buffer_time_minutes ?? 0;
        $slotInterval = 30; // 30-minute intervals

        // Get existing appointments for this staff on this date
        $existingAppointments = Appointment::where('staff_id', $schedule->staff_id)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('status', '!=', 'cancelled')
            ->get();

        $currentTime = $startTime->copy();
        
        // Generate time slots in 30-minute intervals
        while ($currentTime->copy()->addMinutes($serviceDuration)->lte($endTime)) {
            $slotStart = $currentTime->copy();
            $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);
            
            // Skip break time if defined
            if ($schedule->break_start && $schedule->break_end) {
                $breakStart = Carbon::parse($schedule->break_start);
                $breakEnd = Carbon::parse($schedule->break_end);
                
                if ($slotStart->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $currentTime->addMinutes($slotInterval);
                    continue;
                }
            }
            
            // Check if this time slot conflicts with existing appointments
            $hasConflict = $existingAppointments->some(function($appointment) use ($slotStart, $slotEnd) {
                $appointmentStart = Carbon::parse($appointment->appointment_date);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->service->duration_minutes + ($appointment->service->buffer_time_minutes ?? 0));
                
                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            if (!$hasConflict) {
                $timeSlots[] = [
                    'time' => $slotStart->format('H:i'),
                    'display' => $slotStart->format('g:i A'),
                    'end_time' => $slotEnd->format('g:i A'),
                ];
            }
            
            $currentTime->addMinutes($slotInterval);
        }

        $this->availableTimeSlots = $timeSlots;
    }

    // Add suggested times feature
    public function getSuggestedTimes()
    {
        if (empty($this->availableTimeSlots)) {
            return [];
        }

        $suggestedTimes = [];
        $now = Carbon::now();
        
        // Morning slots (9 AM - 12 PM)
        $morningSlots = array_filter($this->availableTimeSlots, function($slot) {
            $time = Carbon::parse($slot['time']);
            return $time->hour >= 9 && $time->hour < 12;
        });
        
        if (!empty($morningSlots)) {
            $suggestedTimes['Morning'] = array_slice($morningSlots, 0, 3);
        }
        
        // Afternoon slots (12 PM - 5 PM)
        $afternoonSlots = array_filter($this->availableTimeSlots, function($slot) {
            $time = Carbon::parse($slot['time']);
            return $time->hour >= 12 && $time->hour < 17;
        });
        
        if (!empty($afternoonSlots)) {
            $suggestedTimes['Afternoon'] = array_slice($afternoonSlots, 0, 3);
        }
        
        // Evening slots (5 PM onwards)
        $eveningSlots = array_filter($this->availableTimeSlots, function($slot) {
            $time = Carbon::parse($slot['time']);
            return $time->hour >= 17;
        });
        
        if (!empty($eveningSlots)) {
            $suggestedTimes['Evening'] = array_slice($eveningSlots, 0, 3);
        }
        
        return $suggestedTimes;
    }

    public function selectQuickTime($time)
    {
        $this->appointment_time = $time;
        $this->quickTimeSelection = true;
    }

    public function mount()
    {
        // Set default appointment date to today
        $this->appointment_date = Carbon::today()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        // Combine date and time
        $appointmentDateTime = Carbon::parse($this->appointment_date . ' ' . $this->appointment_time);

        // Check for conflicts one more time
        $conflict = Appointment::where('staff_id', $this->staff_id)
            ->where('appointment_date', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            session()->flash('error', 'This time slot is no longer available. Please select another time.');
            $this->loadAvailableTimeSlots();
            return;
        }

        // Generate appointment number
        $appointmentNumber = 'APT-' . str_pad(Appointment::count() + 1, 6, '0', STR_PAD_LEFT);

        // Create the first appointment
        $appointment = Appointment::create([
            'client_id' => $this->client_id,
            'service_id' => $this->service_id,
            'staff_id' => $this->staff_id,
            'appointment_date' => $appointmentDateTime,
            'end_time' => $appointmentDateTime->copy()->addMinutes(Service::find($this->service_id)->duration_minutes),
            'appointment_number' => $appointmentNumber,
            'notes' => $this->notes,
            'status' => $this->status,
            'service_price' => Service::find($this->service_id)->price,
            'is_recurring' => $this->is_recurring,
            'recurring_pattern' => $this->is_recurring ? $this->recurring_pattern : null,
            'recurring_end_date' => $this->is_recurring ? $this->recurring_end_date : null,
        ]);

        // Create recurring appointments if needed
        if ($this->is_recurring) {
            $this->createRecurringAppointments($appointment);
        }

        session()->flash('success', 'Appointment created successfully!');
        return redirect()->route('admin.appointments.index');
    }

    public function createRecurringAppointments($parentAppointment)
    {
        $currentDate = Carbon::parse($this->appointment_date);
        $endDate = Carbon::parse($this->recurring_end_date);
        $service = Service::find($this->service_id);
        $appointmentNumber = $parentAppointment->appointment_number;
        $counter = 1;

        while ($currentDate->lte($endDate)) {
            // Calculate next appointment date based on pattern
            switch ($this->recurring_pattern) {
                case 'weekly':
                    $currentDate->addWeek();
                    break;
                case 'biweekly':
                    $currentDate->addWeeks(2);
                    break;
                case 'monthly':
                    $currentDate->addMonth();
                    break;
            }

            if ($currentDate->lte($endDate)) {
                // Check if staff is available on this date
                $schedule = Schedule::where('staff_id', $this->staff_id)
                    ->where('day_of_week', $currentDate->dayOfWeek)
                    ->where('is_active', true)
                    ->first();

                if ($schedule) {
                    // Check for conflicts
                    $conflict = Appointment::where('staff_id', $this->staff_id)
                        ->where('appointment_date', $currentDate->format('Y-m-d') . ' ' . $this->appointment_time)
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if (!$conflict) {
                        $appointmentDateTime = $currentDate->copy()->setTimeFromTimeString($this->appointment_time);
                        
                        Appointment::create([
                            'client_id' => $this->client_id,
                            'service_id' => $this->service_id,
                            'staff_id' => $this->staff_id,
                            'appointment_date' => $appointmentDateTime,
                            'end_time' => $appointmentDateTime->copy()->addMinutes($service->duration_minutes),
                            'appointment_number' => $appointmentNumber . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT),
                            'notes' => $this->notes,
                            'status' => 'pending',
                            'service_price' => $service->price,
                            'is_recurring' => true,
                            'recurring_pattern' => $this->recurring_pattern,
                            'recurring_end_date' => $this->recurring_end_date,
                        ]);
                        
                        $counter++;
                    }
                }
            }
        }
    }

    public function render()
    {
        $clients = Client::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->orderBy('created_at', 'desc')->get();

        $services = Service::where('is_active', true)->orderBy('name')->get();

        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->orderBy('created_at', 'desc')->get();

        return view('livewire.admin.appointments.create', [
            'clients' => $clients,
            'services' => $services,
            'staff' => $staff,
            'suggestedTimes' => $this->getSuggestedTimes(),
        ])->layout('layouts.admin');
    }
}
