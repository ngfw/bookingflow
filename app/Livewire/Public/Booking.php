<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class Booking extends Component
{
    // Step management
    public $currentStep = 1;
    public $totalSteps = 4;

    // Service selection
    public $selectedCategory = '';
    public $selectedService = '';
    public $services = [];
    public $categories = [];

    // Date and time selection
    public $selectedDate = '';
    public $selectedTime = '';
    public $availableDates = [];
    public $availableTimeSlots = [];
    public $selectedStaff = '';

    // Client information
    public $clientName = '';
    public $clientEmail = '';
    public $clientPhone = '';
    public $clientNotes = '';

    // Booking confirmation
    public $bookingConfirmed = false;
    public $appointmentId = null;

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required',
        'clientName' => 'required|string|max:255',
        'clientEmail' => 'required|email|max:255',
        'clientPhone' => 'required|string|max:20',
        'clientNotes' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadServices();
        $this->loadAvailableDates();
    }

    public function loadCategories()
    {
        $this->categories = \App\Models\Category::orderBy('name')->get();
    }

    public function loadServices()
    {
        $query = Service::where('is_active', true)
            ->where('online_booking_enabled', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $this->services = $query->orderBy('name')->get();
    }

    public function updatedSelectedCategory()
    {
        $this->loadServices();
        $this->selectedService = '';
        $this->resetTimeSelection();
    }

    public function updatedSelectedService()
    {
        $this->resetTimeSelection();
        $this->loadAvailableDates();
    }

    public function resetTimeSelection()
    {
        $this->selectedDate = '';
        $this->selectedTime = '';
        $this->availableTimeSlots = [];
        $this->selectedStaff = '';
    }

    public function loadAvailableDates()
    {
        if (!$this->selectedService) {
            $this->availableDates = [];
            return;
        }

        $dates = [];
        $startDate = Carbon::today();
        
        // Load next 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Check if any staff is available on this day
            $hasAvailableStaff = $this->checkStaffAvailability($date);
            
            if ($hasAvailableStaff) {
                $dates[] = [
                    'date' => $date->format('Y-m-d'),
                    'display' => $date->format('M j, Y'),
                    'day' => $date->format('l'),
                ];
            }
        }

        $this->availableDates = $dates;
    }

    public function checkStaffAvailability($date)
    {
        $service = Service::find($this->selectedService);
        if (!$service) return false;

        $availableStaff = Staff::whereHas('services', function($query) {
            $query->where('service_id', $this->selectedService);
        })->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        foreach ($availableStaff as $staff) {
            $schedule = Schedule::where('staff_id', $staff->id)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if ($schedule) {
                return true;
            }
        }

        return false;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableTimeSlots();
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->selectedService || !$this->selectedDate) {
            $this->availableTimeSlots = [];
            return;
        }

        $service = Service::find($this->selectedService);
        $appointmentDate = Carbon::parse($this->selectedDate);

        // Get available staff for this service
        $availableStaff = Staff::whereHas('services', function($query) {
            $query->where('service_id', $this->selectedService);
        })->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        $allTimeSlots = [];

        foreach ($availableStaff as $staff) {
            $schedule = Schedule::where('staff_id', $staff->id)
                ->where('day_of_week', $appointmentDate->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (!$schedule) continue;

            $timeSlots = $this->generateTimeSlotsForStaff($schedule, $service, $appointmentDate, $staff);
            $allTimeSlots = array_merge($allTimeSlots, $timeSlots);
        }

        // Sort time slots by time
        usort($allTimeSlots, function($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        $this->availableTimeSlots = $allTimeSlots;
    }

    public function generateTimeSlotsForStaff($schedule, $service, $appointmentDate, $staff)
    {
        $timeSlots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $serviceDuration = $service->duration_minutes;
        $bufferTime = $service->buffer_time_minutes ?? 0;
        $slotDuration = $serviceDuration + $bufferTime;

        // Get existing appointments for this staff on this date
        $existingAppointments = Appointment::where('staff_id', $staff->id)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('status', '!=', 'cancelled')
            ->get();

        $currentTime = $startTime->copy();
        
        while ($currentTime->addMinutes($slotDuration)->lte($endTime)) {
            $slotStart = $currentTime->copy()->subMinutes($slotDuration);
            $slotEnd = $currentTime->copy();
            
            // Check if this time slot conflicts with existing appointments
            $hasConflict = $existingAppointments->some(function($appointment) use ($slotStart, $slotEnd) {
                $appointmentStart = Carbon::parse($appointment->appointment_date);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->service->duration_minutes);
                
                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            if (!$hasConflict) {
                $timeSlots[] = [
                    'time' => $slotStart->format('H:i'),
                    'display' => $slotStart->format('g:i A'),
                    'end_time' => $slotEnd->format('g:i A'),
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->user->name,
                ];
            }
        }

        return $timeSlots;
    }

    public function selectTimeSlot($timeSlot)
    {
        $this->selectedTime = $timeSlot['time'];
        $this->selectedStaff = $timeSlot['staff_id'];
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function confirmBooking()
    {
        $this->validate();

        // Check if time slot is still available
        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        
        $conflict = Appointment::where('staff_id', $this->selectedStaff)
            ->where('appointment_date', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            session()->flash('error', 'This time slot is no longer available. Please select another time.');
            $this->loadAvailableTimeSlots();
            return;
        }

        // Create or find client
        $user = User::where('email', $this->clientEmail)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $this->clientName,
                'email' => $this->clientEmail,
                'password' => Hash::make('temporary_password_' . rand(1000, 9999)),
                'role' => 'client',
                'phone' => $this->clientPhone,
                'is_active' => true,
            ]);

            Client::create([
                'user_id' => $user->id,
                'preferred_contact' => 'email',
                'visit_count' => 0,
                'total_spent' => 0,
                'loyalty_points' => 0,
            ]);
        }

        $client = $user->client;

        // Create appointment
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $this->selectedService,
            'staff_id' => $this->selectedStaff,
            'appointment_date' => $appointmentDateTime,
            'notes' => $this->clientNotes,
            'status' => 'pending',
            'total_price' => Service::find($this->selectedService)->price,
        ]);

        $this->appointmentId = $appointment->id;
        $this->bookingConfirmed = true;
        $this->currentStep = $this->totalSteps;
    }

    public function render()
    {
        return view('livewire.public.booking');
    }
}
