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
use App\Models\SalonSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class MobileBooking extends Component
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

    // Mobile-specific features
    public $showServiceDetails = false;
    public $showTimePicker = false;
    public $quickBookMode = false;

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required',
        'clientName' => 'required|string|max:255',
        'clientEmail' => 'required|email|max:255',
        'clientPhone' => 'required|string|max:20',
        'clientNotes' => 'nullable|string|max:1000',
    ];

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->selectedService = '';
        $this->showServiceDetails = false;
        $this->loadServices();
    }

    public function updatedSelectedCategory()
    {
        $this->selectedService = '';
        $this->showServiceDetails = false;
        $this->loadServices();
    }

    public function updatedSelectedService()
    {
        $this->selectedDate = '';
        $this->selectedTime = '';
        $this->availableTimeSlots = [];
        $this->showTimePicker = false;
    }

    public function mount()
    {
        $this->loadCategories();
        $this->loadServices();
        $this->loadAvailableDates();
        
        // Enable quick book mode for mobile
        if (isMobile()) {
            $this->quickBookMode = true;
        }
    }

    public function loadCategories()
    {
        $this->categories = \App\Models\Category::orderBy('name')->get();
    }

    public function loadServices()
    {
        $query = Service::where('is_active', true);
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        
        $this->services = $query->orderBy('name')->get();
    }

    public function loadAvailableDates()
    {
        $this->availableDates = [];
        $startDate = now();
        
        // Get maximum booking days from salon settings
        $salonSettings = SalonSetting::getDefault();
        $maxDays = $salonSettings->getMaxBookingDays();
        
        for ($i = 0; $i < $maxDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $this->availableDates[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'display' => $date->format('M j'),
                'is_today' => $date->isToday(),
                'is_tomorrow' => $date->isTomorrow(),
            ];
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedTime = '';
        $this->selectedStaff = '';
        $this->loadAvailableTimeSlots();
        $this->showTimePicker = true;
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->selectedDate || !$this->selectedService) {
            return;
        }

        $this->availableTimeSlots = [];
        $service = Service::find($this->selectedService);
        
        if (!$service) {
            return;
        }
        
        $selectedDate = Carbon::parse($this->selectedDate);
        
        // Get all staff members who can perform this service
        $staffMembers = Staff::whereHas('services', function($query) {
            $query->where('service_id', $this->selectedService);
        })->get();

        foreach ($staffMembers as $staff) {
            // Get staff working hours for the selected date
            $schedule = Schedule::where('staff_id', $staff->id)
                ->where('date', $selectedDate->format('Y-m-d'))
                ->where('status', 'available')
                ->first();

            if (!$schedule) {
                continue;
            }

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $breakStart = $schedule->break_start ? Carbon::parse($schedule->break_start) : null;
            $breakEnd = $schedule->break_end ? Carbon::parse($schedule->break_end) : null;

            // Generate time slots
            $currentTime = $startTime->copy();
            $salonSettings = SalonSetting::getDefault();
            $slotInterval = $salonSettings->getBookingSettings()['booking_time_slots'];
            
            while ($currentTime->copy()->addMinutes($service->duration_minutes)->lte($endTime)) {
                $slotTime = $currentTime->copy();
                $slotEndTime = $slotTime->copy()->addMinutes($service->duration_minutes);
                
                // Skip break time
                if ($breakStart && $breakEnd && 
                    ($slotTime->between($breakStart, $breakEnd) || $slotEndTime->between($breakStart, $breakEnd))) {
                    $currentTime->addMinutes($slotInterval);
                    continue;
                }

                // Check if slot is available
                $appointmentDateTime = $selectedDate->copy()->setTimeFromTimeString($slotTime->format('H:i:s'));
                
                $conflict = Appointment::where('staff_id', $staff->id)
                    ->where('appointment_date', $appointmentDateTime)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$conflict) {
                    $this->availableTimeSlots[] = [
                        'time' => $slotTime->format('H:i:s'),
                        'display' => $slotTime->format('g:i A'),
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->user->name,
                        'available' => true,
                    ];
                }

                $currentTime->addMinutes($slotInterval);
            }
        }

        // Sort by time
        usort($this->availableTimeSlots, function($a, $b) {
            return strcmp($a['time'], $b['time']);
        });
    }

    public function selectTimeSlot($slot)
    {
        $this->selectedTime = $slot['time'];
        $this->selectedStaff = $slot['staff_id'];
    }

    public function toggleServiceDetails($serviceId)
    {
        if ($this->showServiceDetails && $this->selectedService == $serviceId) {
            $this->showServiceDetails = false;
            $this->selectedService = '';
        } else {
            $this->showServiceDetails = true;
            $this->selectedService = $serviceId;
        }
    }
    
    public function selectService($serviceId)
    {
        $this->selectedService = $serviceId;
        $this->showServiceDetails = true;
        
        // Reset date/time selections when changing service
        $this->selectedDate = '';
        $this->selectedTime = '';
        $this->availableTimeSlots = [];
        $this->showTimePicker = false;
        $this->selectedStaff = '';
    }

    public function quickSelectService($serviceId)
    {
        $this->selectedService = $serviceId;
        $this->nextStep();
    }

    public function quickSelectToday()
    {
        $today = now()->format('Y-m-d');
        $this->selectDate($today);
    }

    public function quickSelectTomorrow()
    {
        $tomorrow = now()->addDays(1)->format('Y-m-d');
        $this->selectDate($tomorrow);
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
        return view('livewire.public.mobile-booking');
    }
}

