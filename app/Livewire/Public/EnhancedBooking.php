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
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class EnhancedBooking extends Component
{
    // Step management
    public $currentStep = 1;
    public $totalSteps = 4;

    // Service selection
    public $selectedCategory = '';
    public $selectedService = '';
    public $services = [];
    public $categories = [];
    public $recommendedServices = [];
    public $packageDeals = [];

    // Date and time selection
    public $selectedDate = '';
    public $selectedTime = '';
    public $availableDates = [];
    public $availableTimeSlots = [];
    public $selectedStaff = '';
    public $nextAvailableSlot = null;

    // Client information
    public $clientName = '';
    public $clientEmail = '';
    public $clientPhone = '';
    public $clientNotes = '';

    // Booking confirmation
    public $bookingConfirmed = false;
    public $appointmentId = null;

    // Enhanced features
    public $showServiceDetails = false;
    public $showTimePicker = false;
    public $quickBookMode = false;
    public $loyaltyPoints = 0;
    public $staffAvailability = [];

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
        $this->loadRecommendedServices();
        $this->loadPackageDeals();
        $this->loadStaffAvailability();
        $this->findNextAvailableSlot();
        
        // Enable quick book mode for mobile
        if (isMobile()) {
            $this->quickBookMode = true;
        }
    }

    public function loadCategories()
    {
        $this->categories = Category::orderBy('name')->get();
    }

    public function loadServices()
    {
        $query = Service::with('category');
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        
        $this->services = $query->orderBy('name')->get();
    }

    public function loadRecommendedServices()
    {
        // Get popular services based on booking frequency
        $this->recommendedServices = Service::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->limit(3)
            ->get();
    }

    public function loadPackageDeals()
    {
        // Define package deals
        $this->packageDeals = collect([
            [
                'name' => 'Beauty Essentials',
                'description' => 'Haircut + Styling + Basic Manicure',
                'price' => 85,
                'savings' => 15,
                'services' => [1, 2, 3] // Service IDs
            ],
            [
                'name' => 'Pamper Package',
                'description' => 'Facial + Massage + Pedicure',
                'price' => 120,
                'savings' => 25,
                'services' => [4, 5, 6] // Service IDs
            ],
            [
                'name' => 'Complete Makeover',
                'description' => 'Hair + Makeup + Nails + Consultation',
                'price' => 180,
                'savings' => 40,
                'services' => [1, 7, 8, 9] // Service IDs
            ]
        ]);
    }

    public function loadStaffAvailability()
    {
        $this->staffAvailability = Staff::with('user')
            ->get()
            ->map(function ($staff) {
                $isAvailable = $this->isStaffAvailable($staff->id);
                return [
                    'id' => $staff->id,
                    'name' => $staff->user->name,
                    'specialty' => $staff->specialty ?? 'General',
                    'available' => $isAvailable,
                    'rating' => $staff->rating ?? 4.5
                ];
            });
    }

    public function isStaffAvailable($staffId)
    {
        // Check if staff has any appointments in the next 2 hours
        $nextTwoHours = Carbon::now()->addHours(2);
        $hasAppointment = Appointment::where('staff_id', $staffId)
            ->where('appointment_date', '<=', $nextTwoHours)
            ->where('status', '!=', 'cancelled')
            ->exists();
            
        return !$hasAppointment;
    }

    public function findNextAvailableSlot()
    {
        $tomorrow = Carbon::tomorrow();
        $firstAvailable = Appointment::where('appointment_date', '>=', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date')
            ->first();
            
        if ($firstAvailable) {
            $this->nextAvailableSlot = [
                'date' => $firstAvailable->appointment_date->format('M j, Y'),
                'time' => $firstAvailable->appointment_date->format('g:i A')
            ];
        }
    }

    public function loadAvailableDates()
    {
        $this->availableDates = [];
        $startDate = Carbon::today();
        
        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);
            $this->availableDates[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'display' => $date->format('M j')
            ];
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableTimeSlots();
        $this->showTimePicker = true;
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->selectedDate || !$this->selectedService) {
            return;
        }

        $service = Service::find($this->selectedService);
        $selectedDate = Carbon::parse($this->selectedDate);
        
        // Get staff who can perform this service
        $availableStaff = Staff::whereHas('services', function ($query) {
            $query->where('service_id', $this->selectedService);
        })->get();

        $this->availableTimeSlots = [];
        $startTime = $selectedDate->copy()->setTime(9, 0); // 9 AM
        $endTime = $selectedDate->copy()->setTime(18, 0); // 6 PM

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($service->duration_minutes);
            
            // Check if any staff is available for this slot
            foreach ($availableStaff as $staff) {
                $conflict = Appointment::where('staff_id', $staff->id)
                    ->where('appointment_date', '>=', $startTime)
                    ->where('appointment_date', '<', $slotEnd)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$conflict) {
                    $this->availableTimeSlots[] = [
                        'time' => $startTime->format('H:i'),
                        'display' => $startTime->format('g:i A'),
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->user->name
                    ];
                    break; // Found available staff for this slot
                }
            }
            
            $startTime->addMinutes(30); // 30-minute intervals
        }
    }

    public function selectTimeSlot($slot)
    {
        $slotData = is_string($slot) ? json_decode($slot, true) : $slot;
        $this->selectedTime = $slotData['time'];
        $this->selectedStaff = $slotData['staff_id'];
    }

    public function toggleServiceDetails($serviceId)
    {
        if ($this->showServiceDetails && $this->selectedService == $serviceId) {
            $this->showServiceDetails = false;
        } else {
            $this->showServiceDetails = true;
            $this->selectedService = $serviceId;
        }
    }

    public function quickBook($type)
    {
        // Pre-select services based on quick book type
        switch ($type) {
            case 'express':
                $this->selectedService = 1; // Assuming service ID 1 is express
                break;
            case 'premium':
                $this->selectedService = 2; // Assuming service ID 2 is premium
                break;
        }
        
        $this->nextStep();
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
            'appointment_number' => 'APT' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ]);

        $this->appointmentId = $appointment->id;
        $this->bookingConfirmed = true;
        $this->currentStep = $this->totalSteps;
        
        // Load loyalty points for display
        $this->loyaltyPoints = $client->loyalty_points ?? 0;
    }

    public function render()
    {
        return view('livewire.public.enhanced-booking');
    }
}
